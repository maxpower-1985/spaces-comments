<template>
  <transition name="fade">
    <div id="post-comment-excerpt">
      <div id="excerpt" class="card-divider no-padding flex-dir-row">
        <div class="user small">
          <div class="avatar-container">
            <div class="clipped square">
              <img
                :src="commentExcerpt.author_avatar_urls_48"
                id
                width="100%"
                height="auto"
                v-bind:title="commentExcerpt.author"
              />
            </div>
          </div>
        </div>
        <div class="card-header padding-medium excerpt-container">
          <!--Hier die Ausgaben-->
          <small class="flex flex-dir-row align-middle">
            {{ commentExcerpt.comment_display_name }}
            <span class="dot-after"></span>
            <span>
              {{ commentExcerpt.date | moment(this.dateFormat) }}
              {{ this.commentTranslate.at }}
              {{ commentExcerpt.date | moment(this.timeFormat) }}
            </span>
          </small>
          <div class="card-header padding-medium"></div>
          <div class="comment-excerpt">
            <div
              v-if="
                commentExcerpt.texts.length &&
                commentExcerpt.texts[0] !== '\n' &&
                commentExcerpt.texts[0] !== ''
              "
              class="comment-excerpt-text"
            >
              <div v-for="text in commentExcerpt.texts" v-bind:key="text">
                <p v-html="text"></p>
              </div>
            </div>
            <div
              v-if="
                commentExcerpt.images.length &&
                commentExcerpt.images[0] !== '\n' &&
                commentExcerpt.images[0] !== ''
              "
              class="comment-excerpt-image"
            >
              <div
                v-for="image in commentExcerpt.images.slice(0, 1)"
                v-bind:key="image"
              >
                <p v-html="image"></p>
              </div>
            </div>
            <div
              v-if="
                commentExcerpt.videos.length &&
                !commentExcerpt.images.length &&
                commentExcerpt.videos[0] !== '\n' &&
                commentExcerpt.videos[0] !== '' &&
                commentExcerpt.videos[0] !== 'default'
              "
              class="comment-excerpt-video"
            >
              <div
                v-for="video in commentExcerpt.videos.slice(0, 1)"
                v-bind:key="video"
              >
                <figure class="video">
                  <p v-html="video"></p>
                </figure>
              </div>
            </div>
            <div
              v-else-if="commentExcerpt.videos[0] == 'default'"
              class="comment-excerpt-video"
            >
              <figure class="video">
                <img src="../img/play_video.png" />
              </figure>
            </div>
          </div>
        </div>
        <div class="edit-bar align-self-top">
          <button
            title="close"
            v-on:click.prevent="closeExcerpt"
            type="button"
            class="fa fa-window-close spaces-editor-post-close-button"
          ></button>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  name: "post-comment-excerpt",
  props: {
    commentExcerpt: {},
    commentAuthor: String,
  },
  data() {
    return {
      msg: "msg:post-comment-excerpt",
      commentTranslate: window.commentTranslate,
      dateFormat: window.commentUser.current.dateFormat,
      timeFormat: window.commentUser.current.timeFormat,
    };
  },
  methods: {
    closeExcerpt: function () {
      this.$emit("closePostCommentExcerpt");
    },
  },
};
</script>
