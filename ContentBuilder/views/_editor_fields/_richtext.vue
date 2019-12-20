<template>
  <div class="mb-3" :class="{ 'col-6' : field.size == 'half', 'col-12' : field.size == 'full' }">
    <div class="card shadow">
      <div class="card-header border-0">
        <h3 class="mb-0">{{ field.title }} <p class="m-0" v-if="field.info"><small>{{ field.info }}</small></p></h3>
      </div>
      <div class="card-body pt-0">
        <div class="row">
          <div class="col-12">
            <div class="form-group" :class="{ 'shown-source' : htmlOn }">
              <div ref="editor"></div>
              <textarea class="form-control" v-model="model[field.id]" ref="source" v-show="htmlOn"></textarea>
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
  data: function() {
    return {
      htmlOn: false
    }
  },
  methods: {
    htmlChange: function() {
      if (this.htmlOn == false) {
        this.model[this.field.id] = this.$refs.editor.children[1].innerHTML
        this.htmlOn = true;
      } else {
        this.$refs.editor.children[1].innerHTML = this.model[this.field.id]
        this.htmlOn = false
      }
    }
  },
  mounted: function() {
    var self = this

    if (this.$refs.editor) {
      window.pell.init({
        element: this.$refs.editor,
        onChange: function(html) {
          self.model[self.field.id] = html
        },
        styleWithCSS: false,
        defaultParagraphSeparator: 'div',
        actions: ['bold','italic','underline','strikethrough',
        {name: 'justifyLeft', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M4 19H20V21H4zM4 15H15V17H4zM4 11H20V13H4zM4 3H20V5H4zM4 7H15V9H4z"/></svg>', title: 'Align Left', result: function result() { return pell.exec('justifyLeft'); }},
        {name: 'justifyCenter', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M4 19H20V21H4zM7 15H17V17H7zM4 11H20V13H4zM4 3H20V5H4zM7 7H17V9H7z"/></svg>', title: 'Align Center', result: function result() { return pell.exec('justifyCenter'); }},
        {name: 'justifyRight', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M4 19H20V21H4zM9 15H20V17H9zM4 11H20V13H4zM4 3H20V5H4zM9 7H20V9H9z"/></svg>', title: 'Align Right', result: function result() { return pell.exec('justifyRight'); }},
        {name: 'justifyFull', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M4 7H20V9H4zM4 3H20V5H4zM4 11H20V13H4zM4 15H20V17H4zM6 19H18V21H6z"/></svg>', title: 'Align Justify', result: function result() { return pell.exec('justifyFull'); }},
        'heading1','heading2','olist','ulist',
        {name: 'subscript', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M18.707 12.707L17.293 11.293 13 15.586 13 6 11 6 11 15.586 6.707 11.293 5.293 12.707 12 19.414z"/></svg>', title: 'Subscript', result: function result() { return pell.exec('subscript'); }},
        {name: 'superscript', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M11 8.414L11 18 13 18 13 8.414 17.293 12.707 18.707 11.293 12 4.586 5.293 11.293 6.707 12.707z"/></svg>', title: 'Superscript', result: function result() { return pell.exec('superscript'); }},
        'link',
        {name: 'unlink', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M16.949 14.121L19.071 12c1.948-1.949 1.948-5.122 0-7.071-1.95-1.95-5.123-1.948-7.071 0l-.707.707 1.414 1.414.707-.707c1.169-1.167 3.072-1.169 4.243 0 1.169 1.17 1.169 3.073 0 4.243l-2.122 2.121c-.247.247-.534.435-.844.57L13.414 12l1.414-1.414-.707-.707c-.943-.944-2.199-1.465-3.535-1.465-.235 0-.464.032-.691.066L3.707 2.293 2.293 3.707l18 18 1.414-1.414-5.536-5.536C16.448 14.573 16.709 14.361 16.949 14.121zM10.586 17.657c-1.169 1.167-3.072 1.169-4.243 0-1.169-1.17-1.169-3.073 0-4.243l1.476-1.475-1.414-1.414L4.929 12c-1.948 1.949-1.948 5.122 0 7.071.975.975 2.255 1.462 3.535 1.462 1.281 0 2.562-.487 3.536-1.462l.707-.707-1.414-1.414L10.586 17.657z"/></svg>', title: 'Unlink', result: function result() { return pell.exec('unlink'); }},
        {name: 'undo', icon: '<svg style="transform:scaleX(-1)" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M18,13c0,1.603-0.624,3.109-1.758,4.242C15.109,18.376,13.603,19,12,19s-3.109-0.624-4.243-1.758S6,14.603,6,13 c0-1.602,0.624-3.109,1.757-4.243C8.651,7.864,9.778,7.293,11,7.089V10l5-4l-5-4v3.069C9.243,5.287,7.616,6.071,6.343,7.343 C4.832,8.855,4,10.864,4,13c0,2.137,0.832,4.146,2.343,5.656C7.854,20.168,9.863,21,12,21c2.138,0,4.146-0.832,5.656-2.344 C19.168,17.146,20,15.138,20,13H18z"/></svg>', title: 'Undo', result: function result() { return pell.exec('undo'); }},
        {name: 'redo', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M18,13c0,1.603-0.624,3.109-1.758,4.242C15.109,18.376,13.603,19,12,19s-3.109-0.624-4.243-1.758S6,14.603,6,13 c0-1.602,0.624-3.109,1.757-4.243C8.651,7.864,9.778,7.293,11,7.089V10l5-4l-5-4v3.069C9.243,5.287,7.616,6.071,6.343,7.343 C4.832,8.855,4,10.864,4,13c0,2.137,0.832,4.146,2.343,5.656C7.854,20.168,9.863,21,12,21c2.138,0,4.146-0.832,5.656-2.344 C19.168,17.146,20,15.138,20,13H18z"/></svg>', title: 'Redo', result: function result() { return pell.exec('redo'); }},
        {name: 'html', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M7.375 16.781l1.25-1.562L4.601 12l4.024-3.219-1.25-1.562-5 4C2.138 11.409 2 11.696 2 12s.138.591.375.781L7.375 16.781zM16.625 7.219l-1.25 1.562L19.399 12l-4.024 3.219 1.25 1.562 5-4C21.862 12.591 22 12.304 22 12s-.138-.591-.375-.781L16.625 7.219z"/><path transform="rotate(102.527 12 12)" d="M2.78 11H21.219V13.001H2.78z"/></svg>', title: 'Show source', result: function result() { return self.htmlChange() }}
        ]
      })

      this.$refs.editor.children[1].innerHTML = this.model[this.field.id] ? this.model[this.field.id] : ''
    }
  }
}
</script>