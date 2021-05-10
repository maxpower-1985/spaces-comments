<template>
  <div v-bind:id="'post-comment-' + comment.id">
    <div></div>
    <div class="card-divider no-padding flex-dir-row">
      <div class="user small">
        <!-- {{#author_thumbnail}} -->
        <div class="avatar-container">
          <div class="clipped square">
            <img
              :src="comment.author_avatar_urls_48"
              id
              width="100%"
              height="auto"
              v-bind:data-toggle="'comment-' + comment.id + '-avatar-dropdown'"
              v-bind:title="comment.comment_display_name"
            />
          </div>
        </div>
        <!--{{/author_thumbnail}}-->
        <!--{{#author_dropdown}}-->
        <div
          v-bind:id="'comment-' + comment.id + '-avatar-dropdown'"
          class="dropdown-pane card static"
          data-position="right"
          data-alignment="top"
          data-dropdown
          data-auto-focus="true"
          data-h-offset="6"
          data-close-on-click="true"
        >
          <div class="card-divider padding-button">
            <!--@todo Create anonymous dropdown menu if such user not exists -->
            <a v-bind:href="blogProfile + comment.author"
              ><!--{{ commentTranslate.goToProfileOf }}-->
              {{ comment.comment_display_name }}</a
            >
          </div>
          <div class="card-divider padding-button">
            <a
              v-bind:href="
                scheme + '://' + host + path + '/author/' + comment.author_name
              "
              >{{ commentTranslate.showAllpostsOf }}
              <!--{{ comment.author_name }}--></a
            >
          </div>
        </div>
        <!--/author_dropdown -->
      </div>

      <div class="card-header padding-medium">
        <!--#comment_meta -->
        <small class="flex flex-dir-row align-middle">
          <!--@todo Create anonymous dropdown menu if such user not exists -->
          {{ comment.comment_display_name }}
          <span class="dot-after"></span>
          <span
            >{{ comment.date | moment(this.dateFormat) }}
            {{ this.commentTranslate.at }}
            {{ comment.date | moment(this.timeFormat) }}</span
          >
        </small>
        <!--/comment_meta -->
        <!--#comment_excerpt -->
        <div class="card-header padding-medium">
          <div class="excerpt-comment" v-if="comment.parent != 0">
            <a :href="'#post-comment-' + comment.parent">
              <!--<a :href="comment.excerptLink">-->
              <div
                class="card-divider no-padding flex-dir-row excerpt-container"
              >
                <div class="user small" v-if="comment.parentAvatar != ''">
                  <!--{{#author_thumbnail}}-->
                  <div class="avatar-container">
                    <div class="clipped square">
                      <img
                        :src="comment.parentAvatar"
                        id
                        width="100%"
                        height="auto"
                        v-bind:title="comment.parentAuthor"
                      />
                    </div>
                  </div>
                </div>
                <!--{{/author_thumbnail}}-->
                <!--#comment_meta -->
                <div class="card-header padding-medium">
                  <small
                    class="flex flex-dir-row align-middle"
                    v-if="comment.parentDate != ''"
                  >
                    {{ comment.parent_comment_display_name }}
                    <span class="dot-after"></span>
                    <span
                      >{{ comment.parentDate | moment(this.dateFormat) }}
                      {{ this.commentTranslate.at }}
                      {{ comment.parentDate | moment(this.timeFormat) }}</span
                    >
                  </small>
                  <!--#parent excerpt-->
                  <div class="card-header padding-medium"></div>
                  <div
                    v-bind:id="'comment-excerpt-' + comment.parent"
                    class="comment-excerpt"
                  >
                    <div
                      v-if="
                        comment.parentExcerpt.text.length &&
                        comment.parentExcerpt.text[0] !== '\n' &&
                        comment.parentExcerpt.text[0] !== ''
                      "
                      class="comment-excerpt-text"
                    >
                      <div
                        v-for="text in comment.parentExcerpt.text"
                        v-bind:key="text"
                      >
                        <p v-html="text"></p>
                      </div>
                    </div>
                    <div
                      v-if="
                        comment.parentExcerpt.image.length &&
                        comment.parentExcerpt.image[0] !== '\n' &&
                        comment.parentExcerpt.image[0] !== ''
                      "
                      class="comment-excerpt-image"
                    >
                      <div
                        v-for="image in comment.parentExcerpt.image.slice(0, 1)"
                        v-bind:key="image"
                      >
                        <p v-html="image"></p>
                      </div>
                    </div>
                    <div
                      v-if="
                        comment.parentExcerpt.video.length &&
                        !comment.parentExcerpt.image.length &&
                        comment.parentExcerpt.video[0] !== '\n' &&
                        comment.parentExcerpt.video[0] !== '' &&
                        comment.parentExcerpt.video[0] !== 'default'
                      "
                      class="comment-excerpt-video"
                    >
                      <div
                        v-for="video in comment.parentExcerpt.video.slice(0, 1)"
                        v-bind:key="video"
                      >
                        <figure class="video">
                          <p v-html="video"></p>
                        </figure>
                      </div>
                    </div>
                    <div
                      v-else-if="comment.parentExcerpt.video[0] == 'default'"
                      class="comment-excerpt-video"
                    >
                      <figure class="video">
                      <!-- <img src="./assets/play_video.png" />-->
											<img src="../img/play_video.png"/>
                      </figure>
                    </div>
                  </div>
                  <!-- /parent excerpt -->
                </div>
                <!--/comment_meta -->
              </div>
            </a>
          </div>
        </div>
        <!--/comment_excerpt -->
        <!--#comment_content -->
        <div class="comment-content user-generated s-fg-a-c1-parent">
          <span v-html="comment.content"></span>
        </div>
        <!--/comment_content-->
      </div>
      <!--#button...menu-->
      <div class="edit-bar align-self-top">
        <button
          v-bind:id="'dropdown-menu-button-' + dropdown_menu[0].dropdown_id"
          class="dots"
          type="button"
          v-bind:data-toggle="
            'comment-' + dropdown_menu[0].dropdown_id + '-dropdown'
          "
        ></button>
      </div>
      <!--/button...menu -->
    </div>
    <!--#dropdown_menu -->
    <dropdown_menu v-bind:dropdown_menus="dropdown_menu"></dropdown_menu>
    <!--/dropdown_menu -->
  </div>
</template>
<script>
import dropdown_menu from "./dropdown-menu.vue";
export default {
  name: "post-comment",
  props: {
    comment: [],
  },
  components: {
    dropdown_menu,
  },
  data() {
    return {
      host: window.restApi.host,
      scheme: window.restApi.scheme,
      path: window.restApi.path,
      nonce: window.restApi.nonce,
      lang: window.commentUser.current.lang,
      commentTranslate: window.commentTranslate,
      dateFormat: window.commentUser.current.dateFormat,
      timeFormat: window.commentUser.current.timeFormat,
      blogProfile: window.commentUser.current.blogProfile,
      dropdown_menu: [],
    };
  },
  methods: {
    createDropdownMenu() {
      var commentPost = window.commentPost;
      var commentUser = window.commentUser;
      //Owner of the post, admin, superadmin or with permission moderate_comments
      if (
        commentPost.current_post_author == commentUser.current.id ||
        commentUser.current.allcaps.administrator === true ||
        commentUser.current.allcaps.moderate_comments === true ||
        commentUser.superadmin === true
      ) {
        this.dropdown_menu.push({
          dropdown_id: this.comment.id,
          html: "",
          info: [
            {
              link: "",
              text: "",
            },
          ],
          buttons: [
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=editcomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.editComment,
              class: "js-editcomment",
              callback: "",
            },
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=replycomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.replyComment,
              class: "js-replycomment",
              callback: "replyToComment",
            },
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=trashcomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.deleteComment,
              class: "alert js-deletecomment",
              callback: "deleteComment",
            },
          ],
        });
      } /*Owner comment*/ else if (
        this.comment.author == commentUser.current.id
      ) {
        this.dropdown_menu.push({
          dropdown_id: this.comment.id,
          html: "",
          info: [],
          buttons: [
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=editcomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.editComment,
              class: "js-editcomment",
              callback: "",
            },
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=replycomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.replyComment,
              class: "js-replycomment",
              callback: "replyToComment",
            },
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=trashcomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.deleteComment,
              class: "alert js-deletecomment",
              callback: "deleteComment",
            },
          ],
        });
      } /*Default*/ else {
        this.dropdown_menu.push({
          dropdown_id: this.comment.id,
          html: "",
          info: [],
          buttons: [
            {
              link:
                this.scheme +
                "://" +
                this.host +
                this.path +
                "/wp-admin/comment.php?c=" +
                this.comment.id +
                "&action=replycomment&_wpnonce=" +
                this.nonce,
              text: this.commentTranslate.replyComment,
              class: "js-replycomment",
              callback: "replyToComment",
            },
          ],
        });
      }
      return;
    },
  },
  mounted: function () {
    $(this.$el).foundation();
  },
  beforeMount: function () {
    this.createDropdownMenu();
  },
};
</script>
