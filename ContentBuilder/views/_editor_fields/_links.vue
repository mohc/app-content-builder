<template>
  <div class="mb-3" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }">
    <div class="card shadow">
      <div class="card-header border-0">
        <h3 class="mb-0">{{ field.title }} <p class="m-0" v-if="field.info"><small>{{ field.info }}</small></p></h3>
      </div>
      <div class="card-body pt-0" is="draggable" v-model="model[field.id]" draggable=".row" handle=".drag">
        <div class="row align-items-center" v-for="(link,index) in model[field.id]">
          <div class="col-5">
            <div class="form-group">
              <select id="item-type" class="form-control" v-model="link.type" @change="clearValue(link)">
                <option value="">Select Link Type</option>
                <option value="pages">Page</option>
                <option value="articles">Article</option>
                <option value="blogs">Blog</option>
                <option value="custom_collections">Manual Collection</option>
                <option value="smart_collections">Automatic Collection</option>
                <option value="products">Product</option>
                <option value="url">URL</option>
              </select>
            </div>
          </div>
          <div class="col-6">

            <div class="form-group" v-if="link.type == 'url'">
              <label class="form-control-label position-absolute is">is</label>
              <input type="text" class="form-control" v-model="link.value">
            </div>

            <div class="form-group" v-if="link.type && link.type != 'url'">
              <label class="form-control-label position-absolute is">is</label>
              <div>
                <label class="placeholder">{{ link.title }}</label>
                <input type="text" class="form-control input-id" v-model="link.value" readonly @click.prevent="openModal(link)">
                <a class="btn btn-sm btn-secondary" @click.prevent="openModal(link)">Select</a>
              </div>
            </div>

          </div>
          <div class="col-1 text-right mt--3">
            <a class="btn btn-icon-only drag">
              <i class="fas fa-arrows-alt-v"></i>
            </a>
            <a class="btn btn-icon-only text-danger" @click.prevent="remove(link)">
              <i class="fas fa-trash-alt"></i>
            </a>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <a class="btn btn-secondary" @click.prevent="add" :class="{ 'disabled': max }">Add Link</a>
          </div>
        </div>
      </div>
    </div>

    <component is="inc-ContentBuilder-modal" :active="modal" />

  </div>
</template>

<script>
module.exports = {
  props: ['field', 'model']
}
</script>

<script>
module.exports = {
  props: ['field', 'model'],
  data: function() {
    return {
      modal: '',
      selected: null
    }
  },
  computed: {
    max: function() {
      if (this.field.max && this.model[this.field.id]) {
        if (this.model[this.field.id].length == this.field.max) {
          return true
        }
      }

      return false
    }
  },
  methods: {
    add: function() {
      if (!this.model[this.field.id]) {
        arr = []
      } else {
        arr = this.model[this.field.id]
      }
      arr.push({type: '', value: ''})
      Vue.set(this.model, this.field.id, arr)
    },
    remove: function(link) {
      this.model[this.field.id].splice(this.model[this.field.id].indexOf(link), 1)
    },
    openModal: function(link) {
      this.selected = link
      this.modal = link.type
    },
    clearValue: function(link) {
      link.value = ''
      delete link.title
    }
  }
}
</script>

<style>
  .input-id { width: calc(100% - 90px); display: inline-block; margin-right: 20px; }
  .is { left: -4px; top: 12px; }
  label.placeholder { position: absolute; top: 2px; left: 20px; background: #e9ecef; padding: 10px; font-size: 14px; width: calc(100% - 140px); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; pointer-events: none; }
</style>
