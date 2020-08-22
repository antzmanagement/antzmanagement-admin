<template>
 <v-footer class="justify-center" color="#292929" height="100">
      
      <div
        class="title font-weight-light grey--text text--lighten-1 text-center"
      >&copy; {{ (new Date()).getFullYear() }} — Antz Village Website — Made with 💜 by Simon Ng Sian Siong</div>
    </v-footer>
</template>

<script>
export default {
  props: {
    source: String
  },
  data: () => ({
  }),
};
</script>