

<script>
export default {
  data: () => ({
    absolute: false,
    opacity: 0.46,
    zIndex: 999,
  }),
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {},
};
</script>
<template>
  <v-overlay
    :absolute="absolute"
    v-show="isLoading"
    :opacity="opacity"
    :value="isLoading"
    :z-index="zIndex"
    color="white"
    light
  >
    <circle-spin :loading="true" v-show="isLoading"></circle-spin>
  </v-overlay>
</template>