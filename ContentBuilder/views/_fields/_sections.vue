<template>
  <div class="field-edit-inner" data-type="sections">
    <div class="field-edit-header" :class="{ 'inner-open' : model.open }" @click="model.open = !model.open">
      <h3 class="mb-0">{{ model.title }} <small :class="{ 'ml-3': model.title && model.title != '' }">SECTIONS</small></h3>
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
        <div :class="{ 'col-3' : model.size == 'half', 'col-1' : model.size == 'full' }">
          <div class="form-group">
            <label class="form-control-label">Maximum</label>
            <input type="text" class="form-control" v-model="model.max"> 
          </div>
        </div>
        <div :class="{ 'col-3' : model.size == 'half', 'col-2' : model.size == 'full' }">
          <div class="form-group">
            <label class="form-control-label">Field size</label>
            <select class="form-control" v-model="model.size">
              <option value="full">Full Width</option>
              <option value="half">Half Width</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <label class="form-control-label btn-block">Blocks</label>

          <div class="inner alt" v-show="model.blocks && model.blocks.length > 0" is="draggable" v-model="model.blocks" draggable=".field-wrapper" handle=".drag">
            <div class="field-wrapper col-12" v-for="(block, index) in model.blocks" :class="{ 'mt-3' : index > 0 }">
              <div class="field-edit">
                <a class="drag"><i class="fas fa-arrows-alt-v"></i></a>
                <a class="remove text-danger" @click.prevent="remove(block)"><i class="fas fa-trash-alt"></i></a>
                <div class="field-edit-body" style="display:block">
                  <div class="row align-items-center">
                    <div class="col-4">
                      <div class="form-group">
                        <label class="form-control-label">Title</label>
                        <input type="text" class="form-control" v-model="block.title">
                      </div>
                    </div> 
                    <div class="col-4">
                      <div class="form-group">
                        <label class="form-control-label">ID</label>
                        <input type="text" class="form-control" v-model="block.id"> 
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="form-group">
                        <label class="form-control-label">Info</label>
                        <input type="text" class="form-control" v-model="block.info"> 
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="add-field-buttons">
                        <a class="text-white btn btn-sm btn-primary mb-1" @click.prevent="addField(field.id, block)" v-for="field in fieldTypes">{{ field.label }}</a>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="inner mt-3 row" v-show="block.fields && block.fields.length > 0" is="draggable" v-model="block.fields" draggable=".field-wrapper" handle=".drag">
                        <div class="field-wrapper" v-for="(field, findex) in block.fields" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }">
                          <div class="field-edit">
                            <a class="drag"><i class="fas fa-arrows-alt-v"></i></a>
                            <a class="remove text-danger" @click.prevent="removeField(field, block)"><i class="fas fa-trash-alt"></i></a>
                            <component :is="'inc-ContentBuilder_fields-' + field.type" :model="field" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <a class="btn btn-secondary mt-2" @click.prevent="add">Add Block</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
module.exports = {
  props: ['model'],
  data: function() {
    return {
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
    add: function() {
      if (!this.model.blocks) {
        Vue.set(this.model, 'blocks', [])
      }
      this.model.blocks.push({ title: '', id: '', info: '' })
    },
    addField: function(id, block) {
      if (!block.fields) {
        Vue.set(block, 'fields', [])
      }
      block.fields.push({type: id, size: 'full', open:true})
    },
    remove: function(block) {
      this.model.blocks.splice(this.model.blocks.indexOf(block), 1)
    },
    removeField: function(field, block) {
      block.fields.splice(block.fields.indexOf(field), 1)
    }
  }
}
</script>