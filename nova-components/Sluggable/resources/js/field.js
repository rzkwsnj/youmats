import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-Sluggable', IndexField)
  app.component('detail-Sluggable', DetailField)
  app.component('form-Sluggable', FormField)

})
