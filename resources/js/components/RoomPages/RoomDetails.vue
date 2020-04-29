<template>
  <v-container>
    <v-row justify="center" no-gutters class="mb-12" v-for="(roomType, i) in roomTypes" :key="i">
      <v-col cols="6"  height="100%">
        <v-card tile outlined>
          <v-list-item three-line>
            <v-list-item-content>
              <v-list-item-title
                class="text-justify d-inline-block display-2 mx-12 my-10"
              >{{roomType.name}} - RM {{roomType.price}}</v-list-item-title>
              <!-- <v-list-item-subtitle class="text-justify d-inline-block title mx-12">{{roomType.desc}}</v-list-item-subtitle> -->
              <v-list-item-subtitle>
                <v-responsive class="text-justify title mx-12">{{roomType.desc}}</v-responsive>
              </v-list-item-subtitle>
              <v-list-item-subtitle class="mx-10 my-8">
                <v-btn text>Button</v-btn>
                <v-btn text>Button</v-btn>
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-card>
      </v-col>
      <v-col cols="6" height="100%">

        <v-card height="100%" tile outlined >
          <v-img :aspect-ratio="4/3" height="100%" :src="roomType.imgpath"></v-img>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    roomTypes: {
      type: Array,
      default: () => [
        {
          name: "Premium",
          price: 290.0,
          desc:
            "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
          imgpath:
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278994/217486835_jw9ndq.jpg"
        },

        {
          name: "Normal",
          price: 210.0,
          desc:
            "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
          imgpath:
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278990/182381214_kdfpz3.jpg"
        },

        {
          name: "Owner",
          price: "N/A",
          desc:
            "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
          imgpath:
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278992/badhostels_o9brym.jpg"
        }
      ]
    }
  },
  data: () => ({}),
  created() {
    this.$vuetify.theme.dark = false;
    // this.showLoadingAction();
    // this.getRoomTypesAction({ pageNumber: -1, pageSize: -1 }).then(data => {
    //   this.roomTypes = data.data;
    //   this.endLoadingAction();
    // });
  },
  methods: {
    ...mapActions({
      getRoomTypesAction: "getRoomTypes",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    })
  }
};
</script>