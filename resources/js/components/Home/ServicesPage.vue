<template>
  <v-app>
    <home-navbar></home-navbar>

    <v-content>
      <section id="services" class="grey lighten-3">
        <v-container>
          <v-row no-gutters class="ma-12" >
            <v-col cols="6">
              <v-card
                outlined
                tile
                class="pa-12 d-flex justify-center align-center"
                color="#80A6AD"
                height="100%"
              >
                <v-container>
                  <v-row no-gutters justify="start" align="center" class="ma-5">
                    <v-col cols="2">
                      <div
                        class="d-inline-block display-3 text-left font-weight-black text--secondary"
                      >{{title}}</div>
                    </v-col>
                  </v-row>
                  <v-row no-gutters justify="start" align="center" class="ma-5">
                    <v-col cols="12">
                      <div
                        class="d-inline-block headline text-left font-weight-thin text-white"
                      >{{slogan}}</div>
                    </v-col>
                  </v-row>
                  <v-divider class="ma-5"></v-divider>
                </v-container>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-card
                outlined
                tile
                class="pa-12 d-flex justify-center align-center"
                color="#D6D6D6"
                height="100%"
              >
                <div class="d-inline-block headline text-left font-weight-light ma-5">{{desc}}</div>
              </v-card>
            </v-col>
          </v-row>

          <v-row class="ma-12" v-for="(service, x) in services" :key="x" >
            <v-col cols="4" :order="x % 2 == 0 ? '1' : '2'">
              <v-card flat tile height="103%">
                <v-img :src="service.images[0]" height="100%"></v-img>
              </v-card>
            </v-col>
            <v-col cols="4" :order="x % 2 == 0 ? '2' : '3'">
              <v-card flat tile class="mb-3" width="100%" height="50%">
                <v-img :src="service.images[1]" height="100%"></v-img>
              </v-card>
              <v-card flat tile class="mt-3" width="100%" height="50%">
                <v-img :src="service.images[2]" height="100%"></v-img>
              </v-card>
            </v-col>
            <v-col cols="4" :order="x % 2 == 0 ? '3' : '1'">
              <v-card
                outlined
                tile
                color="#80A6AD"
                height="30%"
                class="mb-3 d-flex justify-center align-center"
                width="100%"
              >
                <div class="d-inline-block display-1 text-center font-weight-black text--secondary">{{service.text}}</div>
              </v-card>
              <v-card flat tile class="mt-3" width="100%" height="70%">
                <v-img :src="service.images[3]" height="100%"></v-img>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </section>

      <section id="contactUs" class="grey lighten-3">
        <contact-us></contact-us>
      </section>
    </v-content>
    <home-footer></home-footer>
  </v-app>
</template>

<script>
export default {
  props: {
    roomTypes: {
      type: Array,
      default: () => []
    }
  },
  data: () => ({
    title: "Services Provided By Us",
    slogan: "Create the perfect living place",
    desc:
      "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
    services: [
      {
        text: "Kitchen",
        images: [
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147310/1572358152315_nnrnuc.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147308/1524514638493_gzthau.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147306/kitchen-ideas-hbx110119wholehome-015-1572549271_liua8d.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147305/DP18-147587_L1_KitchenCat_Update_hero_1-1_dt_2col_KitchenUpgrade-Default_sud64j.jpg"
        ]
      },
      {
        text: "Gym",
        images: [
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147367/3a3e94901f897423c94e7d450fe94cc317dd2383_woman-standing-in-gym_pen5xf.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147372/OpeningAGym_Feature-1_ko1qio.png",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147385/home-gym-room-in-the-attic-royalty-free-image-1092674152-1565968486.jpg_vhq65g.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147422/gym-wide_znumar.jpg"
        ]
      },
      {
        text: "Laundry",
        images: [
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147459/laundry-room-with-a-washing-machine-royalty-free-image-1586287137.jpg_nbl8sx.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147470/laundry_ahc3ic.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147480/shutterstock_778355935-889x592_ylf3xm.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147492/01_Lights_Laundry-Mistakes-You-Didn_E2_80_99t-Know-You-Were-Making_478483900_Evgeny-Atamanenko_ufoipb.jpg"
        ]
      },
      {
        text: "Transportation",
        images: [
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147549/5db9a60edee01961b42c955e_i7ug5h.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147562/Transit_Cargo1_s7seam.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147579/21220599_web1_M-schools-200410_y5gutc.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147595/Larkmead_School_2C_Abingdon_2C_Oxfordshire_c3qpkt.png"
        ]
      },
      {
        text: "Cleaning",
        images: [
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147641/how-to-hire-a-cleaning-service_ajg0k5.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147660/professional-cleaning-tips_kwf8c5.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147677/midsection-of-woman-cleaning-kitchen-counter-at-royalty-free-image-1585171591_eahizv.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147697/zz1-38_euwzhq.jpg"
        ]
      },
      {
        text: "Management",
        images: [
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147776/Management-Skills_ic9wuq.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147803/importance-good-communication-performance-management_ucy5di.png",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147829/What-Is-Human-Resource-Management-HERO_taeifj.jpg",
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1588147855/RE2uCzt_ovbspa.jpg"
        ]
      },
    ]
  }),
  methods: {}
};
</script>