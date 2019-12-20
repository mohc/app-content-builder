<template>
  <div class="main-content">
    <div class="header bg-gradient-primary" :class="{ 'pb-6' : !loading }">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Sellfino App Store</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark m-0 p-0 text-sm font-weight-600 nobg">
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'apps'"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'ContentBuilder-index'">Content Builder</a></li>
                  <li class="breadcrumb-item active text-light">Edit content</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading || saving">Loading... <span class="badge ml-2"></span></div>
              <a href="" class="btn btn-sm btn-neutral" v-if="!loading" :class="{ disabled: saving }" @click.prevent="save">Save</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--6" v-if="!loading">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">{{ this.item.title }} {{ this.item.first_name }} {{ this.item.last_name }} <p class="m-0"><small v-if="$root.viewPrevData">{{ $root.viewPrevData.group }}</small></p></h3>
            </div>
            <div class="card-body pt-0">
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label for="item-fieldset" class="form-control-label">Fieldset</label>
                    <select id="item-fieldset" class="form-control" v-model="content._fieldset">
                      <option value="">No fieldset was selected</option>
                      <option v-for="fieldset in fieldsets" :value="fieldset.id">{{ fieldset.name }}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-5">
        <component :is="'inc-ContentBuilder_editor_fields-' + field.type" :field="field" :model="content" v-for="(field,findex) in fields" v-if="fields" :key="findex" />
      </div>
      <div class="mb-4"></div>
    </div>
  </div>
</template>

<script>
module.exports = {
  data: function() {
    return {
      loading: true,
      saving: false,
      item: this.$root.viewData,
      content: {},
      fieldsets: []
    }
  },
  computed: {
    fields: function() {
      var self = this

      if (this.content._fieldset) {
        fields = []
        masterFieldset = this.content._fieldset
        this.fieldsets.forEach(function(fieldset) {
          if (fieldset.id == masterFieldset) {
            fields = fieldset.fields
          }
        })

        fields.forEach(function(field) {
          if (self.content[field.id]) {
            val = self.content[field.id]
          } else {
            val = null
          }

          if (field.type == 'links' || field.type == 'gallery' || field.type == 'sections') {
            Vue.set(self.content, field.id, val ? val : [])
          } else if (field.type == 'select') {
            Vue.set(self.content, field.id, val ? val : field.options[0].value)
          } else {
            Vue.set(self.content, field.id, val ? val : '')
          }
        })

        return fields
      } else {
        return null
      }
    }
  },
  methods: {
    save: function() {
      var self = this

      this.saving = true

      url = '/app/ContentBuilder/save'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({
          id: this.item.id, 
          type: this.$root.viewPrevData ? this.$root.viewPrevData.tabActive : 'shop',
          content: this.content,
          blog: this.$root.viewPrevData && this.$root.viewPrevData.tabActive == 'articles' ? this.item.blog_id : false
        })
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.saving = false
        self.$root.showToast('Content saved')
      })
      .catch((error) => {
        alert(error)
        self.saving = false
      })
    }
  },
  mounted: function() {
    var self = this

    if (this.item.id) {

      url = '/app/ContentBuilder/content'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({id: this.item.id, type: this.$root.viewPrevData ? this.$root.viewPrevData.tabActive : 'shop'})
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.loading = false
        self.fieldsets = res.fieldsets
        Vue.set(self, 'content', Object.entries(res.content).length || res.content.length ? res.content : {})
      })
      .catch((error) => {
        alert(error)
        self.loading = false
      })

    } else {
      this.loading = false
    }
  }
}
</script>