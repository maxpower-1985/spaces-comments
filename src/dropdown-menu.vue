<script>
/*
{{!
dropdown_array =
[
	'dropdown_id'       => '',
	'html' => '',
	'info' => [
		[
			'link' => '',
			'text' => '',
		]
	],
	'buttons' => [
		[
			'link' => '',
			'text' => '',
			'class'=> '',
			'callback'=>'',
		]
	]
];
}}
*/
</script>
<template>
  <div>
    <!--dropdown_array => dropdown_id-->
    <div
      v-for="dropdown_menu in dropdown_menus"
      class="dropdown-pane card static"
      data-position="bottom"
      data-alignment="right"
      data-dropdown
      data-auto-focus="true"
      data-v-offset="2"
      data-dropdown-menu
      data-close-on-click-inside="true"
      data-close-on-click="true"
      v-bind:id="'comment-' + dropdown_menu.dropdown_id + '-dropdown'"
      v-bind:key="'comment-' + dropdown_menu.dropdown_id + '-dropdown'"
    >
      <!--dropdown_array => html-->
      <div
        class="card-section padding-small"
        v-if="dropdown_menu.html"
        v-html="dropdown_menu.html"
      ></div>
      <!--dropdown_array => info[]-->
      <div
        class="card-divider padding-button"
        v-if="info.text"
        v-for="info in dropdown_menu.info"
      >
        <small>
          <a :href="info.link">{{ info.text }}</a>
        </small>
      </div>
      <!--dropdown_array => buttons[]-->
      <!-- button  with callback function -->
      <div
        class="card-divider padding-button"
        v-for="button in dropdown_menu.buttons"
        v-if="button.callback"
      >
        <a
          :href="button.link"
          v-bind:class="button.class"
          v-on:click.prevent="
            handleFunctionCall(button.callback, dropdown_menu.dropdown_id)
          "
          >{{ button.text }}</a
        >
      </div>
      <!-- button without callback function  -->
      <div class="card-divider padding-button" v-else>
        <a :href="button.link" v-bind:class="button.class">{{ button.text }}</a>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "dropdown-menu",
  props: {
    dropdown_menus: [
      {
        dropdown_id: Number,
        html: String,
        info: [
          {
            link: String,
            text: String,
          },
        ],
        buttons: [
          {
            link: String,
            text: String,
            class: String,
            callback: String,
          },
        ],
      },
    ],
  },
  data() {
    return {};
  },
  methods: {
    handleFunctionCall: function (callback, dropdownID) {
      console.log("handleFunctionCall[ " + callback + " ]");
      window.bus.$emit(callback, dropdownID);
    },
  },
  mounted: function () {},
  beforeMount: function () {},
};
</script>
