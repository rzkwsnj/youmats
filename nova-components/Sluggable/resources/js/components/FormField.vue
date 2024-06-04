<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText" :full-width-content="fullWidthContent">
    <template #field>
      <input :id="field.attribute" type="text" class="w-full form-control form-input form-input-bordered"
        :class="errorClasses" :placeholder="field.name" v-model="value" />
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import lowerCase from 'lodash/lowerCase';
import slug from 'slugify';

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field', 'translatableLocale'],

  mounted() {
    if (this.shouldRegisterInitialListener) {
      this.registerChangeListener();
    }
  },

  methods: {
    setInitialValue() {
      this.value = this.field.value || '';
    },

    fill(formData) {
      formData.append(this.field.attribute, this.value || '');
    },

    handleChange(value) {
      this.value = value;
    },

    changeListener(value) {
      return (value) => {
        this.value = slugify(value, this.field.separator);
      };
    },

    registerChangeListener() {
      Nova.$on(this.eventName, (value) => {
        this.value = this.slugify(value, this.field.separator);
        console.log(this);

      });
    },

    toggleCustomizeClick() {
      if (this.field.readonly) {
        Nova.$off(this.eventName);
        this.field.readonly = false;
        this.field.extraAttributes.readonly = false;
        this.field.showCustomizeButton = false;
        this.$refs.theInput.focus();
        return;
      }
      this.registerChangeListener();
      this.field.readonly = true;
      this.field.extraAttributes.readonly = true;
    },

    slugify(value, separator = '-') {
      return slug(lowerCase(value), separator);
    },
  },
  computed: {
    shouldRegisterInitialListener() {
      return !this.field.updating;
    },

    eventName() {
      const from = this.field.from.replace('*', this.translatableLocale);
      return `${from}-change`;
    },

    extraAttributes() {
      return this.field.extraAttributes || {};
    },

    prefix() {
      if (this.field.pathPrefix) return this.field.pathPrefix[this.translatableLocale];
    },
  },
};
</script>
