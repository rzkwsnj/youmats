Nova.booting((Vue, router, store) => {
  Vue.component('index-generate-products', require('./components/IndexField'))
  Vue.component('detail-generate-products', require('./components/DetailField'))
  Vue.component('form-generate-products', require('./components/FormField'))
})
