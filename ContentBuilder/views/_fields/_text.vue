<template>
  <div class="field-edit-inner" data-type="text">
    <div class="field-edit-header" :class="{ 'inner-open' : model.open }" @click="model.open = !model.open">
      <h3 class="mb-0">{{ model.title }} <small :class="{ 'ml-3': model.title && model.title != '' }">TEXT</small></h3>
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
      </div>
    </div>
  </div>
</template>

<script>
module.exports = {
  props: ['model']
}
</script>