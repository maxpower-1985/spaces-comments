/**
 * You can later bind this to an element with `commentSection.$mount('#some-id');`
 */
//import PostCommentSection from './post-comment-section.vue';
import VueMoment from 'vue-moment';
import 'moment/locale/de';
import 'moment/locale/fr';

const moment = require('moment');
Vue.use(VueMoment, {
	moment,
});
/*Event Bus*/
window.bus = new Vue();
/* Create global window-object commentSection */
window.commentSection = new Vue({
	render: h => h(require('../vue/post-comment-section.vue').default),
});
