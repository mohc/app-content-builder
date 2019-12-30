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
            <a class="btn btn-secondary" :class="{ 'disabled': max }" @click.prevent="openModal">Add from assets</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" style="display:block" :class="{ show: modal }">
      <div class="modal-dialog modal- modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card bg-secondary border-0 mb-0">
              <button type="button" class="close" @click="modal = false">
                <span>×</span>
              </button>
              <div class="card-header bg-transparent pb-1">
                <div class="mt-0 mb-3 h3 text-capitalize">Uploaded assets</div>
              </div>
              <div class="card-body" :class="{ 'p-0' : !loading }">
                <div class="loading-skeleton" v-if="loading"><div></div><div></div><div></div></div>
                <div v-else>
                  <div class="table-responsive">
                    <table class="table align-items-center table-flush" v-if="list.length">
                      <thead class="thead-light">
                        <tr>
                          <th class="w-80">Asset</th>
                          <th class="w-10"></th>
                        </tr>
                      </thead>
                      <tbody class="list">
                        <tr v-for="(el, index) in list">
                          <td @click="select(el)">
                            <div class="uploaded">
                              <div class="file row align-items-center">
                                <div class="col-1">
                                  <div class="custom-control custom-checkbox mt--4">
                                    <input class="custom-control-input" type="checkbox" :checked="selected.indexOf(el.public_url) != -1">
                                    <label class="custom-control-label"></label>
                                  </div>
                                </div>
                                <div class="col-11 row m-0">
                                  <div class="thumb">
                                    <div class="bg" :style="{ backgroundImage: 'url(' + el.public_url + ')' }"></div>
                                    <div class="ext">{{ el.public_url.substr(el.public_url.lastIndexOf('.') + 1).split('?')[0] }}</div>
                                  </div>
                                  <div class="url">{{ el.public_url.split('/').slice(-1).pop().split('?')[0].substr(14) }}<input readonly v-model="el.public_url" /></div>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-icon-only text-danger" @click.prevent="removeFile(el)">
                              <i class="fas fa-trash-alt"></i>
                            </a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="table align-items-center table-flush" v-else>
                      <thead class="thead-light">
                        <tr>
                          <th>
                            <span v-if="loading">Loading... Please wait</span>
                            <span v-else>There are no assets here</span>
                          </th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <nav>
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: page.prev == null || loading }">
                  <a class="page-link" href="#" @click.prevent="prevPage">
                    <i class="fas fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: page.next == null || loading }">
                  <a class="page-link" href="#" @click.prevent="nextPage">
                    <i class="fas fa-angle-right"></i>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
            <div>
              <button type="button" class="btn btn-secondary" @click="modal = false">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade" :class="{ show: modal }"></div>
  </div>
</template>

<script>
module.exports = {
  props: ['field', 'model'],
  data: function() {
    return {
      uploading: false,
      loading: true,
      modal: false,
      list: [],
      page: {
        prev: null,
        next: 1
      }
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
    },
    selected: function() {
      return this.model[this.field.id].map(function(file) {
        return file.url
      })
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
    },
    openModal: function() {
      var self = this

      this.loading = true
      this.modal = true

      url = '/api/shopify'
      data = {
        page_info: this.page.next === 1 ? null : this.page.next,
        fields: 'key,public_url'
      }

      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({
          endpoint: 'themes/' + this.$root.themeId + '/assets',
          method: 'GET',
          params: data
        })
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.list = res.assets
        self.loading = false
        Vue.set(self, 'page', {
          prev: res.prev ? res.prev : null,
          next: res.next ? res.next : null
        })
      })
      .catch((error) => {
        alert(error)
        self.loading = false
      })
    },
    select: function(item) {
      index = this.selected.indexOf(item.public_url)
      if (index == -1) {
        this.model[this.field.id].push({
          url: item.public_url,
          title: ''
        })
      } else {
        this.model[this.field.id].splice(index, 1)
      }
    },
    nextPage: function() {
      this.openModal()
    },
    prevPage: function() {
      this.page.next = this.page.prev
      this.openModal()
    },
    removeFile: function(file) {
      this.list.splice(this.list.indexOf(file), 1)
      url = '/api/shopify'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({
          endpoint: 'themes/' + this.$root.themeId + '/assets?asset[key]=' + file.key,
          method: 'DELETE',
          params: []
        })
      }

      fetch(url, params)
      .then(errorCheck)
      .catch((error) => {
        alert(error)
      })
    }
  }
}
</script>

<style>
  .uploading { position:absolute; z-index: 9; width: 100%; height: 100%; background: rgba(255,255,255,.7); top: 0; left: 0; }
  .uploading::after { content: ''; position: absolute; display: block; width: 30px; height: 30px; top: 50%; left: 50%; margin-left: -15px; margin-top: -15px; border: 3px solid #32325d; border-right-color: transparent; border-top-color: transparent; border-radius: 50%; -webkit-animation: rotate .5s linear 0s infinite; -moz-animation: rotate .5s linear 0s infinite;  -ms-animation: rotate .5s linear 0s infinite;  -o-animation: rotate .5s linear 0s infinite; animation: rotate .5s linear 0s infinite; }
</style>