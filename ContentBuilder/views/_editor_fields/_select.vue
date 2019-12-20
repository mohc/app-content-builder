<template>
  <div class="mb-3" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }">
    <div class="card shadow">
      <div class="card-header border-0">
        <h3 class="mb-0">{{ field.title }} <p class="m-0" v-if="field.info"><small>{{ field.info }}</small></p></h3>
      </div>
      <div class="card-body pt-0">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <select id="item-fieldset" class="form-control" v-model="model[field.id]">
                <option v-for="option in field.options" :value="option.value">{{ option.label }}</option>
              </select>
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
  mounted: function() {
    if (!this.model[this.field.id]) {
      Vue.set(this.model, this.field.id, this.field.options[0].value)
    }
  }
}
</script>