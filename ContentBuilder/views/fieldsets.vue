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
                  <li class="breadcrumb-item"><a href="" class="text-neutral" @click.prevent="$root.view = 'ContentBuilder-index'">Content Builder</a></li>
                  <li class="breadcrumb-item active text-light">Fieldsets</li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <div class="loading text-sm font-weight-600 text-neutral" v-if="loading || saving">Loading... <span class="badge ml-2"></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt--6">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">Fieldsets <a href="#" class="btn btn-sm btn-primary ml-4 position-absolute" @click.prevent="select('new')" :class="{ disabled: loading }">Add Fieldset</a></h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush" v-if="list.length">
                <thead class="thead-light">
                  <tr>
                    <th class="w-60">Name</th>
                    <th class="w-30">Created at</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody class="list">
                  <tr v-for="(el, index) in list">
                    <th scope="row">
                      <div class="media align-items-center">
                        <div class="media-body">
                          <span class="name mb-0 text-sm">
                            <a class="text-primary" @click.prevent="select(el)">{{ el.name }}</a>
                          </span>
                        </div>
                      </div>
                    </th>
                    <td>{{ date_format(el.created_at) }}</td>
                    <td class="text-right">
                      <a class="btn btn-icon-only" :class="{ 'text-muted' : !el.active, 'text-success' : el.active, disabled: loading || saving }" @click.prevent="toggle(el)">
                        <i class="fas" :class="{ 'fa-toggle-off' : !el.active, 'fa-toggle-on' : el.active }"></i>
                      </a>
                      <a class="btn btn-icon-only text-danger" @click.prevent="remove(el)" :class="{ disabled: loading }">
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
                      <span v-else>There are no fieldsets</span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <div class="card-footer py-4 nobg">
            <nav>
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: page == 1 || loading }">
                  <a class="page-link" href="#" @click.prevent="prevPage">
                    <i class="fas fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: list.length < 50 || loading }">
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
      loading: true,
      saving: false,
      list: [],
      page: 1
   }
  },
  methods: {
    load: function() {
      var self = this

      this.loading = true

      url = '/app/ContentBuilder/fieldsets?page=' + this.page
      params = {
        method: 'GET',
        headers: this.$root.fetchHeaders
      }

      fetch(url, params)
      .then(errorCheck)
      .then(function(res) {
        self.list = res
        self.loading = false
      })
      .catch((error) => {
        alert(error)
        self.loading = false
      })

    },
    select: function(item) {
      this.$root.viewPrevData = {
        page: this.page
      }

      if (item == 'new') {
        item = {
          name: '',
          created_at: current_date(),
          fields: [],
          active: true
        }
      }

      this.$root.viewData = item
      this.$root.view = 'ContentBuilder-edit'
    },
    toggle: function(item) {
      item.active = !item.active

      url = '/app/ContentBuilder/toggle'
      params = {
        method: 'POST',
        headers: this.$root.fetchHeaders,
        body: JSON.stringify({ id: item.id })
      }

      fetch(url, params)
      .then(errorCheck)
      .catch((error) => {
        alert(error)
      })
    },
    remove: function(item) {
      var self = this

      r = confirm('Are you sure?')
      if (r == true) {
        this.loading = true

        url = '/app/ContentBuilder/delete'
        params = {
          method: 'POST',
          headers: this.$root.fetchHeaders,
          body: JSON.stringify({ id: item.id })
        }

        fetch(url, params)
        .then(errorCheck)
        .then(function(res) {
          self.loading = false
          if (self.list.length == 1 && self.page > 1) {
            self.page--
          }
          self.load()
        })
        .catch((error) => {
          alert(error)
          self.loading = false
        })
      }
    },
    nextPage: function() {
      this.page++
      this.load()
    },
    prevPage: function() {
      this.page--
      this.load()
    }
  },
  mounted: function() {
    if (this.$root.viewPrevData) {
      this.page = this.$root.viewPrevData.page
      this.$root.viewPrevData = null
    }
    this.load()
  }
}
</script>