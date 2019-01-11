Nova.booting((Vue, router) => {
    // Re-use old components but we extend the form field
    Vue.component('index-belongs-to-dependency', Vue.component('index-belongs-to-field'));
    Vue.component('detail-belongs-to-dependency', Vue.component('detail-belongs-to-field'));
    Vue.component('form-belongs-to-dependency',
        Vue.component('form-belongs-to-field').extend(require('./components/FormField'))
    );
})
