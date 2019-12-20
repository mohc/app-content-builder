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
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'ContentBuilder-fieldsets'">Fieldsets</a></li>
                  <li class="breadcrumb-item active text-light">Edit Fieldset</li>
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
              <h3 class="mb-0">General <p class="m-0"><small>Set basic settings</small></p></h3>
            </div>
            <div class="card-body pt-0">
              <div class="row">
                <div class="col-10">
                  <div class="form-group">
                    <label for="item-name" class="form-control-label">Name</label>
                    <input type="text" id="item-name" class="form-control" v-model="item.name">
                  </div>
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label for="item-active" class="form-control-label">Active</label>
                    <select id="item-active" class="form-control" v-model="item.active">
                      <option :value="true">Yes</option>
                      <option :value="false">No</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-5 mb-4">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">Fields <p class="m-0"><small>Configure schema - add fields</small></p></h3>
            </div>
            <div class="card-body pt-0">
              <div class="add-field-buttons">
                <a class="text-white btn btn-sm btn-primary mb-1" @click.prevent="add(field.id)" v-for="field in fieldTypes">{{ field.label }}</a>
              </div>
            </div>
          </div>
          <div class="fields row" is="draggable" v-model="item.fields" draggable=".field-wrapper" handle=".drag">
            <div class="field-wrapper" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }" v-for="(field, index) in item.fields" :key="index">
              <div class="field-edit">
                <a class="drag"><i class="fas fa-arrows-alt-v"></i></a>
                <a class="remove text-danger" @click.prevent="remove(field)"><i class="fas fa-trash-alt"></i></a>
                <component :is="'inc-ContentBuilder_fields-' + field.type" :model="field" />
              </div>
            </div>
          </div>
        </div>
      </div>
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
      fieldTypes: [
        { id: 'text', label: 'Text' },
        { id: 'textarea', label: 'Textarea' },
        { id: 'richtext', label: 'Richtext' },
        { id: 'select', label: 'Select' },
        { id: 'links', label: 'Links' },
        { id: 'gallery', label: 'Gallery' },
        { id: 'sections', label: 'Sections' },
      ]
    }
  },
  methods: {
    validate: function(fields ,ids, vm) {
      fields.forEach(function(field, index) {
        if (typeof field.id === 'undefined' || field.id == '') {
          vm.$root.showToast('There is ID missing in one of the fields', true)
          return false;
        } else {
          if (field.type == 'sections') {
            if (field.blocks) {
              field.blocks.forEach(function(block) {
                if (block.id == '') {
                  vm.$root.showToast('There is ID missing in one of the blocks', true)
                  return false;
                }
                if (block.fields) {
                  valid = vm.validate(block.fields, ids, vm)
                  if (!valid) {
                    return false;
                  }
                }
              })
            }
          }
          if (ids.indexOf(field.id) == -1) {
            ids.push(field.id)
          } else {
            vm.$root.showToast('There are some fields with the same ID', true)
            return false;
          }
        }
      })
      return true
    },
    save: function() {
      var self = this

      errors = false

      if (this.item.name == '') {
        self.$root.showToast('Name is required', true)
        errors = true
        return;
      }

      valid = this.validate(this.item.fields, [], this)

      if (!valid) {
        errors = true
        return;
      }

      if (!errors) {
        this.saving = true

        url = '/app/ContentBuilder/update'
        params = {
          method: 'POST',
          headers: this.$root.fetchHeaders,
          body: JSON.stringify(this.item)
        }

        fetch(url, params)
        .then(errorCheck)
        .then(function(res) {
          self.saving = false
          self.$root.showToast('Fieldset saved')
          
          if (res.id) {
            self.item.id = res.id
          }
        })
        .catch((error) => {
          alert(error)
          self.saving = false
        })      
      }
    },
    add: function(id) {
      this.item.fields.push({type: id, size: 'full', open: true})
    },
    remove: function(field) {
      this.item.fields.splice(this.item.fields.indexOf(field), 1)
    }
  },
  mounted: function() {
    var self = this

    if (this.item.id) {

      url = '/app/ContentBuilder/fieldset?id=' + this.item.id
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.loading = false
        self.item = res
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