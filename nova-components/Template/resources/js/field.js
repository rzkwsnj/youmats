import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-template', IndexField)
  app.component('detail-template', DetailField)
  app.component('form-template', FormField)
})
