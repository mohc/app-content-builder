<template>
  <div class="mb-3" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }">
    <div class="card shadow">
      <div class="card-header border-0">
        <h3 class="mb-0">{{ field.title }} <p class="m-0" v-if="field.info"><small>{{ field.info }}</small></p></h3>
      </div>
      <div class="card-body pt-0">
        <div class="add-field-buttons">
          <a class="text-white btn btn-sm btn-primary mb-1" @click.prevent="add(block.id)" v-for="block in field.blocks">{{ block.title }}</a>
        </div>
        <div class="blocks mt-4" v-if="model[field.id] && model[field.id].length" is="draggable" v-model="model[field.id]" draggable=".field-edit" handle=".drag">
          <div class="field-edit" v-for="(block, bindex) in model[field.id]" :class="{ 'inner-fields' : block.open, 'no-fields' : !blocks[block.type].fields || !blocks[block.type].fields.length }" :key="bindex">
            <a class="drag"><i class="fas fa-arrows-alt-v"></i></a>
            <a class="remove text-danger" @click.prevent="remove(block)"><i class="fas fa-trash-alt"></i></a>
            <div class="field-edit-inner">
              <div class="field-edit-header" :class="{ 'inner-open' : block.open }" @click="block.open = !block.open">
                <h3 class="mb-0">{{ blocks[block.type].title }}</h3>
              </div>
              <div class="field-edit-body mt-4" v-if="blocks[block.type].fields && blocks[block.type].fields.length">
                <div class="row align-items-top">
                  <component :is="'inc-ContentBuilder_editor_fields-' + field.type" :field="field"  :model="block.value" v-for="(field, findex) in blocks[block.type].fields" :key="findex" />
                </div>
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
  props: ['field', 'model'],
  computed: {
    blocks: function() {
      arr = {}
      this.field.blocks.forEach(function(block) {
        arr[block.id] = block
      })
      return arr
    }
  },
  methods: {
    add: function(id) {
      if (!this.model[this.field.id]) {
        arr = []
      } else {
        arr = this.model[this.field.id]
      }
      arr.push({type: id, value: {}, open: true})
      Vue.set(this.model, this.field.id, arr)
    },
    remove: function(block) {
      this.model[this.field.id].splice(this.model[this.field.id].indexOf(block), 1)
    }
  }
}
</script>