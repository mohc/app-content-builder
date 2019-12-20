<template>
  <div class="field-edit-inner" data-type="select">
    <div class="field-edit-header" :class="{ 'inner-open' : model.open }" @click="model.open = !model.open">
      <h3 class="mb-0">{{ model.title }} <small :class="{ 'ml-3': model.title && model.title != '' }">SELECT</small></h3>
    </div>
    <div class="field-edit-body mt2">
      <div class="row align-items-center">
        <div :class="{ 'col-6' : model.size == 'half', 'col-3' : model.size == 'full' }">
          <div class="form-group">
            <label class="form-control-label">Title</label>
            <input type="text" class="form-control" v-model="model.title"> 
          </div>
        </div> 
        <div :class="{ 'col-6' : model.size == 'half', 'col-3' : model.size == 'full' }">
          <div class="form-group">
            <label class="form-control-label">ID</label>
            <input type="text" class="form-control" v-model="model.id"> 
          </div>
        </div>
        <div :class="{ 'col-6' : model.size == 'half', 'col-3' : model.size == 'full' }">
          <div class="form-group">
            <label class="form-control-label">Info</label>
            <input type="text" class="form-control" v-model="model.info"> 
          </div>
        </div>
        <div :class="{ 'col-6' : model.size == 'half', 'col-3' : model.size == 'full' }">
          <div class="form-group">
            <label class="form-control-label">Field size</label>
            <select class="form-control" v-model="model.size">
              <option value="full">Full Width</option>
              <option value="half">Half Width</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <label class="form-control-label btn-block">Options</label>
          <div class="inner" v-show="model.options && model.options.length > 0" is="draggable" v-model="model.options" draggable=".row" handle=".drag-inner">
            <div class="row align-items-center" v-for="(option, index) in model.options" :class="{ 'mt-3' : index > 0 }">
              <div class="col-5">
                <label class="form-control-label">Value</label>
                <input type="text" class="form-control" v-model="option.value"> 
              </div>
              <div class="col-6">
                <label class="form-control-label">Label</label>
                <input type="text" class="form-control" v-model="option.label"> 
              </div>
              <div class="col-1 text-right mt-4">
                <a class="btn btn-icon-only text-primary drag-inner">
                  <i class="fas fa-arrows-alt-v"></i>
                </a>
                <a class="btn btn-icon-only text-danger" @click.prevent="remove(option)">
                  <i class="fas fa-trash-alt"></i>
                </a>
              </div>
            </div>
          </div>
          <a class="btn btn-secondary mt-2" @click.prevent="add">Add Option</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
module.exports = {
  props: ['model'],
  methods: {
    add: function() {
      if (!this.model.options) {
        Vue.set(this.model, 'options', [])
      }
      this.model.options.push({ value: '', label: '' })
    },
    remove: function(option) {
      this.model.options.splice(this.model.options.indexOf(option), 1)
    }
  }
}
</script>