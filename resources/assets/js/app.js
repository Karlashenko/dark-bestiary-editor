require('./bootstrap');

import Notifications from 'vue-notification';
import VueRouter from 'vue-router';
import router from './routes';

window.Vue = require('vue');
window.Vue.use(Notifications);
window.Vue.use(VueRouter);

Vue.component('list', require('./components/List.vue'));
Vue.component('toolbar', require('./components/Toolbar.vue'));
Vue.component('navigation', require('./components/Navigation.vue'));
Vue.component('image-gallery', require('./components/fields/ImageGallery.vue'));
Vue.component('selectpicker', require('./components/Fields/Selectpicker.vue'));
Vue.component('flags', require('./components/fields/Flags.vue'));
Vue.component('effect', require('./components/Effect.vue'));
Vue.component('effect-field', require('./components/fields/EffectField.vue'));
Vue.component('talent-field', require('./components/fields/TalentField.vue'));
Vue.component('currency-field', require('./components/fields/CurrencyField.vue'));
Vue.component('i18n-field', require('./components/fields/I18NField.vue'));
Vue.component('icon-field', require('./components/fields/IconField.vue'));
Vue.component('prefab-field', require('./components/fields/PrefabField.vue'));
Vue.component('behaviour-field', require('./components/fields/BehaviourField.vue'));
Vue.component('unit-field', require('./components/fields/UnitField.vue'));
Vue.component('item-field', require('./components/fields/ItemField.vue'));
Vue.component('recipe-field', require('./components/fields/RecipeField.vue'));
Vue.component('relic-field', require('./components/fields/RelicField.vue'));
Vue.component('skill-field', require('./components/fields/SkillField.vue'));
Vue.component('phrase-field', require('./components/fields/PhraseField.vue'));
Vue.component('behaviour-tree-node', require('./components/fields/BehaviourTreeNode.vue'));
Vue.component('attribute-chart', require('./components/fields/AttributeChart.vue'));
Vue.component('mesh-field', require('./components/fields/MeshField.vue'));
Vue.component('dialogue-action', require('./components/DialogueAction.vue'));

const app = new Vue({el: '#app', router: router});
