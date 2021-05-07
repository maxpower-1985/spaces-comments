<template>
  <!-- this fetches all the comments and brings them together -->
  <div id="post-comment-section">
    <post_comment
      v-for="comment in comments"
      v-bind:comment="comment"
      v-bind:key="comment.id"
    ></post_comment>
    <post_comment_excerpt
      v-on:closePostCommentExcerpt="closePostCommentExcerpt"
      v-if="postCommentExcerpt"
      v-bind:commentExcerpt="commentExcerpt"
    ></post_comment_excerpt>
    <post_comment_form
      v-if="commentEditor === 'false'"
      :key="resetComponentPostCommentForm"
    ></post_comment_form>
  </div>
</template>

<script>
import qs from "qs";
import post_comment from "./post-comment.vue";
import post_comment_excerpt from "./post-comment-excerpt.vue";
import post_comment_form from "./post-comment-form.vue";

export default {
  name: "post-comment-section",
  props: {},
  components: {
    post_comment,
    post_comment_excerpt,
    post_comment_form,
  },
  data() {
    return {
      comments: [],
      scheme: window.restApi.scheme,
      restUrl: window.restApi.rest_url,
      postId: window.commentPost.current_post_id,
      host: window.restApi.host,
      postCommentExcerpt: false,
      commentExcerpt: {
        parentId: 0,
        author: "",
        content: "",
        date: "",
        author_avatar_urls_48: "",
        parentDisplayName: "",
        texts: [],
        videos: [],
        images: [],
      },
      resetComponentPostCommentForm: 0,
      lang: window.commentUser.current.lang,
      commentEditor: window.commentEditor,
      commentTranslate: window.commentTranslate,
    };
  },
  methods: {
    getAllCommentsFromPost() {
      var status;
      var message;
      var res;
      var data;
      axios.defaults.headers.common = {
        "X-WP-Nonce":
          typeof window.restApi !== "undefined" ? window.restApi.nonce : "",
        "X-Requested-With": "XMLHttpRequest",
      };
      axios
        .get(
          this.restUrl +
            "wp/v2/comments/?post=" +
            this.postId +
            "&order=asc&orderby=date&per_page=100"
        )
        .then((response) => {
          var counter = 0;
          var i = 0;
          var comment_display_name = "";
          //Handle Success
          res = response;
          status = res.status;
          message = res.statusText;
          counter = res.data.length;
          if (counter > 0) {
            while (i < counter) {
              //Check displayname from author
              if (response.data[i]["display_name"] !== null || "") {
                comment_display_name = response.data[i]["display_name"];
              } else if (response.data[i]["author_name"] !== null || "") {
                comment_display_name = response.data[i]["author_name"];
              } else {
                comment_display_name = "Anonymous User";
              }
              this.comments.push({
                id: response.data[i]["id"],
                author_name: response.data[i]["author_name"],
                author: response.data[i]["author"],
                author_avatar_urls_24:
                  response.data[i]["author_avatar_urls"]["24"],
                author_avatar_urls_48:
                  response.data[i]["author_avatar_urls"]["48"],
                author_avatar_urls_96:
                  response.data[i]["author_avatar_urls"]["96"],
                date: response.data[i]["date"],
                content: response.data[i]["content"]["rendered"],
                link: response.data[i]["link"],
                parent: response.data[i]["parent"],
                post: response.data[i]["post"],
                status: response.data[i]["status"],
                display_name: response.data[i]["display_name"],
                comment_display_name: comment_display_name,
              });
              i++;
            }
          }
          this.getContentFromParent(this.comments);
        })
        .catch((error) => {
          //Handle error
          if (error.response) {
            status = error.response.status;
            message = error.response.data.message;
            console.log(status);
            console.log(message);
          }
        })
        .finally(function () {
          data = {
            message: message,
            status: status,
          };
          /*Send event to bus*/
          window.bus.$emit("getAllCommentsFromPostStatus", data);
        });
    },
    postComment: function (content) {
      var status;
      var message;
      var res;
      var commentPost = window.commentPost;
      var user = window.commentUser;
      var data = {
        post: this.postId,
        author: user.current.id,
        author_email: user.current.email,
        content: content,
        parent: this.commentExcerpt.parentId,
      };
      axios.defaults.headers.common = {
        "X-WP-Nonce":
          typeof window.restApi !== "undefined" ? window.restApi.nonce : "",
        "X-Requested-With": "XMLHttpRequest",
      };
      axios
        .post(this.restUrl + "wp/v2/comments/", qs.stringify(data))
        .then((response) => {
          res = response;
          status = res.status;
          message = res.statusText;
        })
        .catch((error) => {
          if (error.response) {
            status = error.response.status;
            message = error.response.data.message;
            console.log(status);
            console.log(message);
          }
        })
        .finally(() => {
          if (status == 201) {
            var comment_display_name;
            /*Here Adding the display_name*/
            if (res.data.display_name !== null || "") {
              this.comment_display_name = res.data.display_name;
            } else if (res.data.author_name !== null || "") {
              this.comment_display_name = res.data.author_name;
            } else {
              this.comment_display_name = "Anonymous User";
            }
            /*HERE Function adding Comment in Frontend*/
            this.comments.push({
              id: res.data.id,
              author_name: res.data.author_name,
              author: res.data.author,
              author_avatar_urls_24: res.data.author_avatar_urls["24"],
              author_avatar_urls_48: res.data.author_avatar_urls["48"],
              author_avatar_urls_96: res.data.author_avatar_urls["96"],
              date: res.data.date,
              content: res.data.content["rendered"],
              link: res.data.link,
              parent: res.data.parent,
              post: res.data.post,
              status: res.data.status,
              parentAuthor: this.commentExcerpt.authorId,
              parentDate: this.commentExcerpt.date,
              parentContent: this.commentExcerpt.content,
              parentExcerpt: {
                text: this.commentExcerpt.texts,
                video: this.commentExcerpt.videos,
                image: this.commentExcerpt.images,
              },
              parentAvatar: this.commentExcerpt.author_avatar_urls_48,
              parentDisplayName: this.commentExcerpt.display_name,
              comment_display_name: this.comment_display_name,
              parent_comment_display_name: this.commentExcerpt
                .comment_display_name,
            });
            this.closePostCommentExcerpt();
            this.resetComponentPostCommentForm += 1;
          }
          data = {
            message: message,
            status: status,
          };
          /*Send event to bus*/
          window.bus.$emit("postCommentStatus", data);
        });
      return;
    },
    deleteCommentFromPost: function (commentID) {
      //Close Dropdownmenu.
      var dropdownmenu = "comment-" + commentID + "-dropdown";
      var element = document.getElementById(dropdownmenu);
      element.classList.remove("is-open");
      var i = 0;
      var counter = this.comments.length;
      var res;
      var status;
      var message;
      var data;
      if (counter > 0) {
        while (i < counter) {
          if (this.comments[i].id === commentID) {
            if (confirm(this.commentTranslate.askDeleteComment)) {
              axios.defaults.headers.common = {
                "X-WP-Nonce":
                  typeof window.restApi !== "undefined"
                    ? window.restApi.nonce
                    : "",
                "X-Requested-With": "XMLHttpRequest",
              };
              axios
                .delete(this.restUrl + "wp/v2/comments/" + commentID)
                .then((response) => {
                  res = response;
                  status = res.status;
                  message = res.statusText;
                })
                .catch((error) => {
                  if (error.response) {
                    status = error.response.status;
                    message = error.response.data.message;
                    console.log(status);
                    console.log(message);
                  }
                })
                .finally(() => {
                  if (status == 200) {
                    this.$delete(this.comments, i);
                    this.closePostCommentExcerpt();
                  }
                  data = {
                    message: message,
                    status: status,
                  };
                  window.bus.$emit("deleteCommentStatus", data);
                });
              return;
            } else {
              return;
            }
          }
          i++;
        }
      }
    },
    closePostCommentExcerpt: function () {
      this.postCommentExcerpt = false;
      this.commentExcerpt.parentId = 0;
      this.commentExcerpt.author = "";
      this.commentExcerpt.content = "";
      this.commentExcerpt.date = "";
      this.commentExcerpt.author_avatar_urls_48 = "";
      this.commentExcerpt.texts = null;
      this.commentExcerpt.videos = null;
      this.commentExcerpt.images = null;
    },
    replyToComment: function (commentID) {
      var excerpt;
      //Close Dropdownmenu.
      var dropdownmenu = "comment-" + commentID + "-dropdown";
      var element = document.getElementById(dropdownmenu);
      element.classList.remove("is-open");
      //Get the reply comment from list of comments
      var replyComment = this.getCommentById(commentID, this.comments);
      this.commentExcerpt.authorId = replyComment.author;
      this.commentExcerpt.authorName = replyComment.author_name;
      this.commentExcerpt.displayName = replyComment.display_name;
      this.commentExcerpt.content = replyComment.content;
      /* Call helperfunction to create excerpt*/
      excerpt = this.getCommentExcerpt(replyComment.content);
      this.commentExcerpt.texts = excerpt.text;
      this.commentExcerpt.videos = excerpt.video;
      this.commentExcerpt.images = excerpt.image;
      this.commentExcerpt.author_avatar_urls_48 =
        replyComment.author_avatar_urls_48;
      this.commentExcerpt.date = replyComment.date;
      this.commentExcerpt.parentId = commentID;
      this.commentExcerpt.comment_display_name =
        replyComment.comment_display_name;
      /*Show Component postCommentExcerpt*/
      this.postCommentExcerpt = true;
      //Check if global editor exists
      if (this.commentEditor === "true") {
        //Set the cursor into editor and scroll to editor
        spacesPostEditorGlobals.commentEditor.focus();
        document.getElementById("respond").scrollIntoView({
          block: "start",
          inline: "end",
        });
      } else if (document.getElementById("comment") !== null) {
        document.getElementById("comment").focus();
        document.getElementById("respond").scrollIntoView({
          block: "start",
          inline: "end",
        });
      } else {
        console.log("Couldn't load any editor");
        //@todo Fehlerausgabe Coouldn't load any editor
      }
      return;
    },
    getCommentById: function (commentID, comments_list) {
      for (var j = 0; j < comments_list.length; j++) {
        if (comments_list[j].id == commentID) {
          return comments_list[j];
        }
      }
    },
    getContentFromParent: function (commentsList) {
      if (commentsList.length > 0) {
        var counter = commentsList.length;
        var i = 0;
        while (i < counter) {
          if (commentsList[i].parent !== 0) {
            /*Find parent comment*/
            var parentComment = this.comments.filter(function (childComment) {
              return childComment.id === commentsList[i].parent;
            });
            /*Check if parent comment exists*/
            if (parentComment[0] !== undefined) {
              this.comments[i].parentContent = parentComment[0].content;
              this.comments[i].parentExcerpt = this.getCommentExcerpt(
                parentComment[0].content
              );
              if (parentComment[0].display_name !== null || "") {
                this.comments[i].parent_comment_display_name =
                  parentComment[0].display_name;
              } else if (parentComment[0].author_name !== null || "") {
                this.comments[i].parent_comment_display_name =
                  parentComment[0].author_name;
              } else {
                this.comments[i].parent_comment_display_name = "Anonymous User";
              }
              this.comments[i].parentAuthor = parentComment[0].author_name;
              this.comments[i].parentDisplayName =
                parentComment[0].display_name;
              this.comments[i].parentAvatar =
                parentComment[0].author_avatar_urls_24;
              this.comments[i].parentDate = parentComment[0].date;
            } else {
              /*Parent comment is deleted*/
              this.comments[i].parentAvatar = "";
              this.comments[
                i
              ].parentContent = this.commentTranslate.deletedComment;
              this.comments[i].parentDate = "";
              this.comments[i].parentExcerpt = {
                text: [],
                image: "",
                video: "",
              };

              this.comments[i].parentExcerpt.text.push(
                this.commentTranslate.deletedComment
              );
            }
          }
          i++;
        }
      }
      return;
    },
    getCommentExcerpt: function (content) {
      var mediaImage = null;
      var mediaVideo = null;
      var thumbnail = {};
      var i;
      var excerpt = {
        text: [],
        video: [],
        image: [],
      };
      var excerptContent = content;
      /* Create a new regular Expression for finding all videos in the content*/
      var regexVideo = new RegExp(/(<p>)?<iframe.+?<\/iframe>(<\/p>)?/, "g");
      /* Create a new regular Expression for finding all images in the content*/
      var regexImage = new RegExp(/<figure.+?<\/figure>/, "g");
      /* Check if exists regular Expression exists */
      if (true === regexVideo.test(content)) {
        mediaVideo = content.match(regexVideo);
        for (i = 0; i <= mediaVideo.length - 1; i++) {
          content = content.replace(mediaVideo[i], "");
          /* Get the thumbnail */
          thumbnail[i] = this.createThumbnail(mediaVideo[i]);
          excerpt.video.push(thumbnail[i]);
        }
      }
      /* Check if exists regular Expression exists */
      if (true === regexImage.test(content)) {
        mediaImage = content.match(regexImage);
        for (i = 0; i <= mediaImage.length - 1; i++) {
          excerpt.image.push(mediaImage[i]);
          /* Replace the image with space*/
          content = content.replace(mediaImage[i], "");
        }
      }
      /*Create the commentExcerpt with max 300 signs*/
      /*todo Get the Options from the Wordpress max signs for comments*/
      excerpt.text.push(this.getSubstr(content, 300));
      return excerpt;
    },
    setLanguage(lang) {
      this.$moment.locale(lang);
    },
    getSubstr: function (str, maxLength) {
      var temp = str.substr(0, maxLength);
      var strLength = str.length;
      if (temp.lastIndexOf("<") > temp.lastIndexOf(">")) {
        temp = str.substr(0, 1 + str.indexOf(">", temp.lastIndexOf("<")));
      }
      /* Add ... only if the string is bigger than max */
      if (str.length > maxLength) {
        //re-trim if we are in the middle of a word and
        temp = temp.substr(0, Math.min(temp.length, temp.lastIndexOf(" ")));
        temp = temp.concat(" ... ");
      }
      return temp;
    },
    createThumbnail: function (mediaVideo) {
      var i;
      var tmp;
      if (true === mediaVideo.includes("youtube")) {
        var regexYoutube = new RegExp(
          /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/
        );
        //@todo get thumbnail from DailyMotion and Vimeo
        if (true === regexYoutube.test(mediaVideo)) {
          tmp = mediaVideo.match(regexYoutube);
          /* Get youtube thumbnail*/
          return (
            '<img src="https://i3.ytimg.com/vi/' +
            tmp[7] +
            '/hqdefault.jpg" alt="">'
          ); //pass hqdefault.jpg 0,1,2,3 for different sizes like 0.jpg, 1.jpg
        }
      } else if (true === mediaVideo.includes("vimeo")) {
        var regexVimeo = new RegExp(
          /(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/i
        );
        if (true === regexVimeo.test(mediaVideo)) {
          //@todo Create thumbnail function for vimeo (cors)
          tmp = mediaVideo.match(regexVimeo);
          return "default";
        }
      } else {
        return "default";
      }
    },
  },
  mounted: function () {
    this.setLanguage(this.lang);
    this.getAllCommentsFromPost();
  },
  beforeMount: function () {},
  created: function () {
    window.bus.$on("deleteComment", (data) => {
      this.deleteCommentFromPost(data);
    }),
      window.bus.$on("replyToComment", (data) => {
        this.replyToComment(data);
      }),
      window.bus.$on("postComment", (data) => {
        this.postComment(data);
      });
  },
};
</script>
<style lang="css">
@import "css/style.css";
</style>
