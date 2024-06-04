<template>
    <default-field :field="field" :errors="errors" :show-help-text="showHelpText" :full-width-content="true">
        <template slot="field">
            <div v-if="template == null">The selected category doesn't have a template</div>
            <div v-else>
                <div v-for="locale in locales" :style="[locale == 'ar' ? {'border-bottom': '2px solid #7c858e', direction: 'rtl'} : {}]">
                    <div v-for="(item, index) in template" class="my-2">
                        <div v-if="item.word[locale].split('')[0] == '+'">
                            <input type="text" class="form-control form-input form-input-bordered inline-block w-auto mb-1" :class="errorClasses"
                                   :placeholder="item.word[locale].substr(1)" required style="width: 75%"
                                   v-model="current[locale][index]['value']" />
                            <input type="number" class="form-control form-input form-input-bordered inline-block w-auto mb-1"
                                   style="width: 23%;margin: 0 0.5%;border-color: #65bc18;" required
                                   placeholder="index" v-model="current[locale][index]['order']" />
                        </div>
                        <div v-else-if="item.word[locale].split('')[0] == '-'">
                            <input type="text" class="form-control form-input form-input-bordered inline-block w-auto mb-1" :class="errorClasses"
                                   :placeholder="item.word[locale].substr(1).split('-')[0]" required style="width: 75%"
                                   v-model="current[locale][index]['value']" />
                            <input type="number" class="form-control form-input form-input-bordered inline-block w-auto mb-1"
                                   style="width: 23%;margin: 0 0.5%;border-color: #65bc18;" required
                                   placeholder="index" v-model="current[locale][index]['order']" />
                        </div>
                        <div v-else-if="item.word[locale].split('')[0] != null">
                            <input type="text" readonly required
                                   class="form-control form-input form-input-bordered inline-block w-auto mb-1" style="width: 100%"
                                   v-model="current[locale][index]['value'] = item.word[locale]" />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            fields: [],
            locales: ['ar', 'en'],
            template: null,
            current: null,
            category: null
        }
    },
    mounted() {
        this.watchedComponents.forEach(component => {
            let attribute = 'value'
            if(component.field.component === 'belongs-to-field') {
                attribute = 'selectedResource';
            }
            component.$watch(attribute, (value) => {
                this.category = (value && attribute === 'selectedResource') ? value.value : value;
                this.updateResults();
            }, { immediate: true });
        });
    },
    computed: {
        watchedComponents() {
            return this.$parent.$children.filter(component => {
                return this.isWatchingComponent(component);
            })
        },

        endpoint() {
            return this.field.endpoint
                .replace('{'+ this.field.category +'}', this.category ? this.category : '')
                .replace('{model}', this.resourceId ? this.resourceId : null)
        },
    },
    methods: {
        isWatchingComponent(component) {
            return component.field !== undefined && component.field.attribute === this.field.category;
        },

        updateResults() {
            if(this.notWatching() || (this.category != null && this.category !== '')) {
                Nova.request().get(this.endpoint)
                    .then(response => {
                        if (response.data.template != null && response.data.template != '') {
                            this.template = response.data.template;
                            let templateLength = this.template.length;
                            if (response.data.current) {
                                this.current = {
                                    'ar': response.data.current.ar,
                                    'en': response.data.current.en
                                }
                            } else {
                                this.current = {
                                    'ar': {
                                        0: {
                                            'value': '',
                                            'order': ''
                                        },
                                        1: {
                                            'value': '',
                                            'order': ''
                                        },
                                        2: {
                                            'value': '',
                                            'order': ''
                                        },
                                        3: {
                                            'value': '',
                                            'order': ''
                                        },
                                        4: {
                                            'value': '',
                                            'order': ''
                                        },
                                        5: {
                                            'value': '',
                                            'order': ''
                                        },
                                        6: {
                                            'value': '',
                                            'order': ''
                                        },
                                        7: {
                                            'value': '',
                                            'order': ''
                                        },
                                        8: {
                                            'value': '',
                                            'order': ''
                                        },
                                        9: {
                                            'value': '',
                                            'order': ''
                                        },
                                        10: {
                                            'value': '',
                                            'order': ''
                                        },
                                        11: {
                                            'value': '',
                                            'order': ''
                                        },
                                        12: {
                                            'value': '',
                                            'order': ''
                                        }
                                    },
                                    'en': {
                                        0: {
                                            'value': '',
                                            'order': ''
                                        },
                                        1: {
                                            'value': '',
                                            'order': ''
                                        },
                                        2: {
                                            'value': '',
                                            'order': ''
                                        },
                                        3: {
                                            'value': '',
                                            'order': ''
                                        },
                                        4: {
                                            'value': '',
                                            'order': ''
                                        },
                                        5: {
                                            'value': '',
                                            'order': ''
                                        },
                                        6: {
                                            'value': '',
                                            'order': ''
                                        },
                                        7: {
                                            'value': '',
                                            'order': ''
                                        },
                                        8: {
                                            'value': '',
                                            'order': ''
                                        },
                                        9: {
                                            'value': '',
                                            'order': ''
                                        },
                                        10: {
                                            'value': '',
                                            'order': ''
                                        },
                                        11: {
                                            'value': '',
                                            'order': ''
                                        },
                                        12: {
                                            'value': '',
                                            'order': ''
                                        }
                                    }
                                }
                            }
                        } else {
                            this.template = null;
                        }
                    })
            }
        },

        notWatching() {
            return this.field.category === undefined;
        },

        /*
        * Set the initial, internal value for the field.
        */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, JSON.stringify(this.current) || '')
        },
    }
}
</script>
