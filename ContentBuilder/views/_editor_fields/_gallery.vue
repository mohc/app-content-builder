<template>
  <div class="mb-3" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }">
    <div class="card shadow">
      <div class="card-header border-0">
        <h3 class="mb-0">{{ field.title }} <p class="m-0" v-if="field.info"><small>{{ field.info }}</small></p></h3>
      </div>
      <div class="card-body pt-0 position-relative">
        <div class="uploading" v-if="uploading"></div>
        <div class="row">
          <div class="col-12">
            <div class="uploaded" is="draggable" v-model="model[field.id]" draggable=".file" handle=".drag">
              <div class="file row align-items-center p-2 mb-2" v-for="(file, key) in model[field.id]" :key="key" v-if="file.url">
                <div class="col-8 row m-0">
                  <div class="thumb">
                    <div class="bg" :style="{ backgroundImage: 'url(' + file.url + ')' }"></div>
                    <div class="ext">{{ file.url.substr(file.url.lastIndexOf('.') + 1).split('?')[0] }}</div>
                  </div>
                  <div class="url">{{ file.url.split('/').slice(-1).pop().split('?')[0].substr(14) }}<input readonly v-model="file.url" /></div>
                </div>
                <div class="col-3">
                  <input type="text" class="form-control" v-model="file.title" placeholder="Title"> 
                </div>
                <div class="col-1 text-right">
                  <a class="btn btn-icon-only drag">
                    <i class="fas fa-arrows-alt-v"></i>
                  </a>
                  <a class="btn btn-icon-only text-danger" @click.prevent="remove(file)">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div hidden><input type="file" multiple ref="uploadInput" @change="upload" :accept="field.accept ? field.accept : false" /></div>
            <a class="btn btn-secondary" :class="{ 'disabled': max }" @click.prevent="uploadRun">Upload</a>
            <!-- <a class="btn btn-secondary" :class="{ 'disabled': max }">Add from assets</a> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
module.exports = {
  props: ['field', 'model'],
  data: function() {
    return {
      uploading: false
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
    uploadRun: function() {
      this.$refs.uploadInput.click()
    },
    upload: function(e) {
      var self = this
      this.uploading = true

      current = 0

      if (this.model[this.field.id] && this.model[this.field.id].length) {
        current = this.model[this.field.id].length
      }

      maximum = 'none'

      if (this.field.max) {
        maximum = this.field.max - current
      }

      if (maximum == 'none') {
        files = e.target.files
      } else {
        files = Array.prototype.slice.call(e.target.files, 0, maximum)
      }

      formData = new FormData()
      for (var i = 0; i < files.length; i++) {
        formData.append('files[]', files[i])
      }

      url = '/app/ContentBuilder/upload'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: formData
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.uploading = false
        if (!self.model[self.field.id]) {
          Vue.set(self.model, self.field.id, res)
        } else {
          Vue.set(self.model, self.field.id, self.model[self.field.id].concat(res))
        }
      })
      .catch((error) => {
        alert(error)
        self.saving = false
      })
    },
    remove: function(file) {
      this.model[this.field.id].splice(this.model[this.field.id].indexOf(file), 1)
    }
  }
}
</script>

<style>
  .uploading { position:absolute; z-index: 9; width: 100%; height: 100%; background: rgba(255,255,255,.7); top: 0; left: 0; }
  .uploading::after { content: ''; position: absolute; display: block; width: 30px; height: 30px; top: 50%; left: 50%; margin-left: -15px; margin-top: -15px; border: 3px solid #32325d; border-right-color: transparent; border-top-color: transparent; border-radius: 50%; -webkit-animation: rotate .5s linear 0s infinite; -moz-animation: rotate .5s linear 0s infinite;  -ms-animation: rotate .5s linear 0s infinite;  -o-animation: rotate .5s linear 0s infinite; animation: rotate .5s linear 0s infinite; }
</style>