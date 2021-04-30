
<script>
import moment from "moment";
import { mapActions } from "vuex";

export default {
  props: {
    data: {
      type: Array,
      default: [],
    },
    fields: {
      type: Object,
      default: null,
    },
    name: {
      type: String,
      default: null,
    },
    header: {
      type: String,
      default: null,
    },
    
  },
  data() {
    return {};
  },
  created() {
    this.$vuetify.goTo(0);
  },
  methods: {},
};
</script>


<template>
  <download-excel :data="data" type="csv" :name="`${name || null}.csv`" :fields="fields || null" :header="header || null" class="float-right mx-5">
    <slot>
      <v-btn>download</v-btn>
    </slot>
  </download-excel>
</template>
