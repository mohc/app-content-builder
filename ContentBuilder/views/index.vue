<template>
  <div class="main-content">
    <div class="header bg-gradient-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Sellfino App Store</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark m-0 p-0 text-sm font-weight-600 nobg">
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'apps'"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item active text-light">Content Builder</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading">Loading... <span class="badge ml-2"></span></div>
              <a href="#" class="btn btn-sm btn-neutral" :class="{ disabled: loading }" @click.prevent="$root.view = 'ContentBuilder-fieldsets'">Fieldsets</a>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" class="btn mb-1" v-for="tab in tabs" :class="{ 'btn-secondary' : tab.handle != tabActive, 'btn-info pointer-disabled' : tab.handle == tabActive }" @click="load(tab.handle)">{{ tab.name }}</button>
            </div>
          </div>
          <div class="info text-neutral text-sm font-italic"><i class="fas fa-info-circle mr-2"></i> Select resource first to show the elements</div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--5" v-if="tabActive">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col-6">
                  <h3 class="mb-0">Click <span class="text-capitalize">{{ tabActive.replace('_', ' ').slice(0, -1) }}</span> to edit content</h3>
                </div>
                <div class="col-6">
                  <form class="float-right" @submit.prevent="searchRun">
                    <div class="row align-items-center m-0">
                      <div class="form-group m-0">
                        <input type="text" class="form-control text-xs h-auto py-1" :placeholder="searchPlaceholder" v-model="search">
                      </div>
                      <button class="btn btn-primary ml-2 btn-sm" type="submit">Find</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-60">Name</th>
                    <th class="w-30" v-if="tabActive == 'articles' || tabActive == 'blogs' || tabActive == 'pages'">Handle</th>
                    <th class="w-30" v-else>ID</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <th scope="row">
                      <div class="media align-items-center">
                        <a class="avatar rounded-circle mr-3" v-if="el.image">
                          <img :src="img_url(el.image.src, 'small')">
                        </a>
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a class="text-primary" @click="select(el)">
                              <div v-if="el.title">{{ el.title }}</div>
                              <div v-if="el.last_name">{{ el.first_name }} {{ el.last_name }}</div>
                              <div v-if="el.name">{{ el.name }} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span>aa</span></div>
                            </a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td v-if="tabActive == 'articles' || tabActive == 'blogs' || tabActive == 'pages'">
                      {{ el.handle }}
                    </td>
                    <td v-else>
                      {{ el.id }}
                    </td>
                    <td class="text-right">
                      <a :href="admin_link(el.id, tabActive)" class="btn btn-icon-only text-primary" target="_blank">
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
          <div class="card-footer py-4 nobg">
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
      loading: false,
      tabActive: null,
      tabs: [
        { handle: 'articles', name: 'Articles' },
        { handle: 'blogs', name: 'Blogs' },
        { handle: 'customers', name: 'Customers' },
        { handle: 'custom_collections', name: 'Manual Collections' },
        { handle: 'smart_collections', name: 'Automatic Collections' },
        { handle: 'orders', name: 'Orders' },
        { handle: 'pages', name: 'Pages' },
        { handle: 'products', name: 'Products' },
        { handle: 'shop', name: 'Shop' }
      ],
      list: [],
      page: {
        prev: null,
        next: 1
      },
      search: ''
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

      this.loading = true

      if (this.tabActive != handle) {
        this.page = {
          prev: null,
          next: 1
        }
        this.search = ''
      }

      if (handle == 'shop') {

        this.list = []
        this.loading = false
        this.tabActive = handle

        this.$root.viewData = { id: 'shop', title: 'Global' }
        this.$root.view = 'ContentBuilder-editor'

      } else {

        url = '/api/shopify'
        data = {
          page_info: this.page.next === 1 ? null : this.page.next,
          fields: 'id,title,handle,first_name,last_name,name,created_at,commentable,image'
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
          self.tabActive = handle
          Vue.set(self, 'page', {
            prev: res.prev ? res.prev : null,
            next: res.next ? res.next : null
          })
        })
        .catch((error) => {
          alert(error)
        })

      }
    },
    select: function(item) {
      var self = this

      this.$root.viewPrevData = {
        page: this.page,
        search: this.search,
        tabActive: this.tabActive,
        group: this.tabs.filter(function(tab) {
          return tab.handle == self.tabActive
        })[0].name
      }

      this.$root.viewData = item
      this.$root.view = 'ContentBuilder-editor'
    },
    searchRun: function() {
      this.page = {
        prev: null,
        next: 1
      }
      this.loading = true
      this.load(this.tabActive)
    },
    nextPage: function() {
      this.load(this.tabActive)
    },
    prevPage: function() {
      this.page.next = this.page.prev
      this.load(this.tabActive)
    }
  },
  mounted: function() {
    if (this.$root.viewPrevData && this.$root.viewPrevData.tabActive) {
      this.tabActive = this.$root.viewPrevData.tabActive
      this.page = this.$root.viewPrevData.page
      this.search = this.$root.viewPrevData.search
      this.$root.viewPrevData = null
      this.load(this.tabActive)
    }
  }
}
</script>