<template>
  <div id="respond" class="card static comment-reply comment-respond">
    <form
      action="$siteurl/wp-comments-post.php"
      method="post"
      id="commentform"
      class="comment-form flex"
    >
      <div class="avatar small">
        <img :src="avatar" id width="100%" height="auto" />
        <!--$comment_usermarkup-->
      </div>
      <div class="comment-reply-content flex no-padding">
        <textarea
          v-model="comment.content"
          name="comment"
          id="comment"
          tabindex="3"
          class="comment-reply-text margin-0 padding-medium autosizejs"
          v-bind:placeholder="this.commentTranslate.addNewComment + '...'"
          aria-required="true"
          style="resize: vertical"
          required="required"
          onfocus="this.placeholder =''"
          v-on:blur="handleblur"
        ></textarea>
        <!--$comment_id_fields-->
        <input
          v-on:click.prevent="postComment()"
          name="submit"
          type="submit"
          id="comment-submit"
          tabindex="4"
          class="margin-0 padding-medium small button clear success comment-reply-submit load"
          v-bind:value="this.commentTranslate.reply"
          data-toggler=".load"
          data-toggle="comment-submit"
        />
        <span class="s-fg-a-c1-parent">
          <!--$cancel_comment_reply_link-->
        </span>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: "post-comment-form",
  props: {},
  components: {},
  data() {
    return {
      avatar: window.commentUser.avatar_url,
      commentTranslate: window.commentTranslate,
      comment: {
        content: "",
      },
    };
  },
  methods: {
    postComment: function () {
      if (this.comment.content !== "") {
        window.bus.$emit("postComment", this.comment.content);
      }
      return;
    },
    handleblur: function (e) {
      if (e.target.value === "") {
        return (e.target.placeholder =
          this.commentTranslate.addNewComment + "...");
      }
      return;
    },
  },
  mounted: function () {},
  beforeMount: function () {},
  created: function () {
    window.bus.$on("postCommentStatus", (data) => {
      var status = data;
      if (data.status == 201) {
        console.log(data.status + ": " + data.message);
      } else console.log(data.status + ": " + data.message);
    });
  },
};
</script>
