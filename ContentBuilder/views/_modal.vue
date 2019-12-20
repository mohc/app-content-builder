<template>
  <div>
    <div class="modal fade" style="display:block" :class="{ show: active }">
      <div class="modal-dialog modal- modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-body p-0">
            <div class="card bg-secondary border-0 mb-0">
              <button type="button" class="close" @click="$parent.modal = ''">
                <span>×</span>
              </button>
              <div class="card-header bg-transparent pb-1">
                <div class="mt-0 mb-3 h3 text-capitalize">{{ active.replace('_', ' ').slice(0, -1) }}</div>
                <form @submit.prevent="searchRun">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control text-xs h-auto" :placeholder="searchPlaceholder" v-model="search">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-primary btn-md lh-120" type="submit" :class="{ disabled: loading || searching }">
                        <span v-if="searching">Searching ...</span>
                        <span v-else>Search</span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="card-body" :class="{ 'p-0' : !loading }">
                <div class="loading-skeleton" v-if="loading"><div></div><div></div><div></div></div>
                <div v-else>
                  <div class="table-responsive">
                    <table class="table align-items-center table-flush" v-if="list.length">
                      <thead class="thead-light">
                        <tr>
                          <th class="w-60">Name</th>
                          <th class="w-30" v-if="active == 'articles' || active == 'blogs' || active == 'pages'">Handle</th>
                          <th class="w-30" v-else>ID</th>
                          <th class="w-10"></th>
                        </tr>
                      </thead>
                      <tbody class="list">
                        <tr v-for="(el, index) in list">
                          <th scope="row">
                            <div class="media align-items-center">
                              <a class="avatar rounded-circle mr-3" v-if="el.image" @click.prevent="select(el)">
                                <img :src="img_url(el.image.src, 'small')">
                              </a>
                              <div class="media-body">
                                <span class="name mb-0 text-sm">
                                  <a href="" class="text-primary" @click.prevent="select(el)">
                                    <div v-if="el.title">{{ el.title }}</div>
                                    <div v-if="el.last_name">{{ el.first_name }} {{ el.last_name }}</div>
                                    <div v-if="el.name">{{ el.name }} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span>aa</span></div>
                                  </a>
                                </span>
                              </div>
                            </div>
                          </th>
                          <td v-if="active == 'articles' || active == 'blogs' || active == 'pages'">
                            {{ el.handle }}
                          </td>
                          <td v-else>
                            {{ el.id }}
                          </td>
                          <td class="text-right">
                            <a :href="admin_link(el.id, active)" class="btn btn-icon-only text-primary" target="_blank">
                              <i class="fas fa-external-link-alt"></i>
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
                            <span v-else>There are no elements here</span>
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
                <li class="page-item" :class="{ disabled: page.prev == null || loading || searching }">
                  <a class="page-link" href="#" @click.prevent="prevPage">
                    <i class="fas fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: page.next == null || loading || searching }">
                  <a class="page-link" href="#" @click.prevent="nextPage">
                    <i class="fas fa-angle-right"></i>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
            <div>
              <button type="button" class="btn btn-secondary" @click="$parent.modal = ''">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop fade" :class="{ show: active }"></div>

  </div>
</template>

<script>
module.exports = {
  props: ['active'],
  data: function() {
    return {
      loading: true,
      searching: false,
      list: [],
      page: {
        prev: null,
        next: 1
      },
      search: ''
    }
  },
  watch: {
    active: function(val) {
      if (val) {
        this.loading = true
        this.searching = false
        this.page = {
          prev: null,
          next: 1
        },
        this.search = ''
        this.load(val)
      }
    }
  },
  computed: {
    searchPlaceholder: function() {
      switch(this.tabActive) {
        case 'blogs':
        case 'articles':
        case 'pages':
          return 'Enter handle'
          break;

        default:
          return 'Enter ID'
      }
    }
  },
  methods: {
    load: function(handle) {
      var self = this

      if (this.active != handle) {
        this.page = {
          prev: null,
          next: 1
        }
        this.search = ''
        this.loading = true
      }

      url = '/api/shopify'
      data = {
        page_info: this.page.next === 1 ? null : this.page.next,
        fields: 'id,title,handle,first_name,last_name,name,created_at,commentable,image,blog_id'
      }

      if (this.search != '') {
        switch(handle) {
          case 'articles':
          case 'blogs':
          case 'pages':
            data.handle = this.search
          break;

          default:
            data.ids = this.search
        }
      }

      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({
          endpoint: handle,
          method: 'GET',
          params: data
        })
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.list = res[handle]
        self.loading = false
        self.searching = false
        self.active = handle
        Vue.set(self, 'page', {
          prev: res.prev ? res.prev : null,
          next: res.next ? res.next : null
        })
      })
      .catch((error) => {
        alert(error)
      })
    },
    select: function(item) {
      var self = this
      if (this.active == 'articles') {
        params = {
          method: 'POST',
          headers: this.$root.fetchHeaders,
          body: JSON.stringify({
            endpoint: 'blogs/' + item.blog_id,
            method: 'GET',
            params: { fields: 'handle' }
          })
        }

        fetch('/api/shopify', params)
        .then(errorCheck)
        .then(function(res) {
          self.$parent.selected.value = res.blog.handle + '/' + self.$parent.selected.value
          self.$parent.selected = null
        })
        .catch((error) => {
          alert(error)
        })
      }
      this.$parent.selected.value = item.handle
      this.$parent.selected.id = item.id

      if (item.title) {
        this.$parent.selected.title = item.title
      }

      this.$parent.modal = ''
      
      if (this.active != 'articles') {
        this.$parent.selected = null
      }
    },
    searchRun: function() {
      this.page = {
        prev: null,
        next: 1
      }
      this.searching = true
      this.load(this.active)
    },
    nextPage: function() {
      this.load(this.active)
    },
    prevPage: function() {
      this.page.next = this.page.prev
      this.load(this.active)
    }
  }
}
</script>