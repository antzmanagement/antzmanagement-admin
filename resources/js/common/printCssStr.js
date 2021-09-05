const printCss = `
.invoice-box {
    margin: auto;
    padding: 30px;
    border: 1px solid #eee;
    box-shadow: 0 0 10px rgba(0, 0, 0, .15);
    font-size: 10px;
    line-height: 20px;
    font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    color: #555;
}

.height-10 {
  height: 10%;
}

.height-15 {
  height: 15%;
}

.height-20 {
  height: 20%;
}

.height-30 {
  height: 30%;
}

.height-40 {
  height: 40%;
}

.height-50 {
  height: 50%;
}

.height-half {
  height: 50%;
}

.height-60 {
  height: 60%;
}

.height-70 {
  height: 70%;
}

.height-80 {
  height: 80%;
}

.height-90 {
  height: 90%;
}

.height-100 {
  height: 100%;
}

.height-auto {
  height: auto;
}

.height-window-100 {
  height: 100vh;
}
.width-10 {
    width: 10%;
}

.width-20 {
    width: 20%;
}

.width-30 {
    width: 30%;
}

.width-40 {
    width: 40%;
}

.width-50 {
    width: 50%;
}

.width-half {
    width: 50%;
}

.width-60 {
    width: 60%;
}

.width-70 {
    width: 70%;
}

.width-75 {
    width: 75%;
}

.width-80 {
    width: 80%;
}

.width-90 {
    width: 90%;
}

.width-100 {
    width: 100%;
}

.width-auto {
    width: auto;
}

.width-auto {
    width: auto;
}
.thin-border {
    border-width: thin;
    border: solid #dedede 1px;
}

.wrapBorderRed {
  border: solid;
  border-color: red;
}

.wrapBorderBlue {
  border: solid;
  border-color: blue;
}

.wrapBorderGreen {
  border: solid;
  border-color: green;
}
.height-100
h1,
h2,
h3,
h4,
h5,
h6,
.h1,
.h2,
.h3,
.h4,
.h5,
.h6 {
  margin-bottom: 0.5rem;
  font-weight: 500;
  line-height: 1.2;
}

h1,
.h1 {
  font-size: 2.25rem;
}

h2,
.h2 {
  font-size: 1.8rem;
}

h3,
.h3 {
  font-size: 1.575rem;
}

h4,
.h4 {
  font-size: 1.35rem;
}

h5,
.h5 {
  font-size: 1.125rem;
}

h6,
.h6 {
  font-size: 0.9rem;
}

.subtitle td{
    font-size: 10px;
}
.text-align-left {
    text-align: left;
}

.invoice-box table {
    width: 100%;
    line-height: inherit;
    text-align: left;
}

.invoice-box table td {
    padding: 5px;
    vertical-align: top;
}

.invoice-box table tr.invoice td:nth-child(2) {
    text-align: right;
}

.invoice-box table tr.top table td {
    padding-bottom: 20px;
}

.invoice-box table tr.top table td.title {
    font-size: 45px;
    line-height: 30px;
    color: #333;
}

.invoice-box table tr.information table td {
    padding-bottom: 20px;
}

.invoice-box table tr.heading td {
    background: #eee;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}

.invoice-box table tr.details td {
    padding-bottom: 20px;
}

.invoice-box table tr.item td {
    border-bottom: 1px solid #eee;
}

.invoice-box table tr.item.last td {
    border-bottom: none;
}

.invoice-box table tr.total td:nth-child(2) {
    border-top: 2px solid #eee;
    font-weight: bold;
}

.round-border {
  border-radius: 10px;
}

.round-border-light {
  border-radius: 5px;
}

.round-border-big {
  border-radius: 15px;
}
.font-weight-bold{
    font-weight: bold;
}
.small-text {
    font-size: 0.50rem;
    letter-spacing: 0.01em;
}
.overline {
    font-size: 0.75rem;
    letter-spacing: 0.1666666667em;
}

.caption {
    font-size: 0.75rem;
    letter-spacing: 0.025rem;
}
.subtitle1 {
    font-size: 1rem;
    letter-spacing: 0.009375rem;
}
.round-border {
    border-radius: 10px;
}
.text-align-right {
    text-align: right;
}
.text-align-center {
    text-align: center;
}

.flex-items-align-center {
    display: flex;
    align-items: center;
}
.flex-justify-end {
    display: flex;
    justify-content: flex-end;
}
.d-inline-block {
    display: inline-block
}

.no-padding {
  padding: 0
}

.padding-xs {
  padding: .25em;
}

.padding-sm {
  padding: .5em;
}

.padding-md {
  padding: 1em;
}

.padding-lg {
  padding: 1.5em;
}

.padding-xl {
  padding: 3em;
}

.padding-x-xs {
  padding-left: .25em;
  padding-right: .25em;
}

.padding-x-sm {
  padding-left: .5em;
  padding-right: .5em;
}

.padding-x-md {
  padding-left: 1em;
  padding-right: 1em;
}

.padding-x-lg {
  padding-left: 1.5em;
  padding-right: 1.5em;
}

.padding-x-xl {
  padding-left: 3em;
  padding-right: 3em;
}

.padding-y-xs {
  padding-top: .25em;
  padding-bottom: .25em;
}

.padding-y-sm {
  padding-top: .5em;
  padding-bottom: .5em;
}

.padding-y-md {
  padding-top: 1em;
  padding-bottom: 1em;
}

.padding-y-lg {
  padding-top: 1.5em;
  padding-bottom: 1.5em;
}

.padding-y-xl {
  padding-top: 3em;
  padding-bottom: 3em;
}

.padding-top-xs {
  padding-top: .25em;
}

.padding-top-sm {
  padding-top: .5em;
}

.padding-top-md {
  padding-top: 1em;
}

.padding-top-lg {
  padding-top: 1.5em;
}

.padding-top-xl {
  padding-top: 3em;
}

.padding-right-xs {
  padding-right: .25em;
}

.padding-right-sm {
  padding-right: .5em;
}

.padding-right-md {
  padding-right: 1em;
}

.padding-right-lg {
  padding-right: 1.5em;
}

.padding-right-xl {
  padding-right: 3em;
}

.padding-bottom-xs {
  padding-bottom: .25em;
}

.padding-bottom-sm {
  padding-bottom: .5em;
}

.padding-bottom-md {
  padding-bottom: 1em;
}

.padding-bottom-lg {
  padding-bottom: 1.5em;
}

.padding-bottom-xl {
  padding-bottom: 3em;
}

.padding-left-xs {
  padding-left: .25em;
}

.padding-left-sm {
  padding-left: .5em;
}

.padding-left-md {
  padding-left: 1em;
}

.padding-left-lg {
  padding-left: 1.5em;
}

.padding-left-xl {
  padding-left: 3em;
}

.no-margin {
  margin: 0
}

.margin-xs {
  margin: .25em;
}

.margin-sm {
  margin: .5em;
}

.margin-md {
  margin: 1em;
}

.margin-lg {
  margin: 1.5em;
}

.margin-xl {
  margin: 3em;
}

.margin-y-xs {
  margin: .25em 0;
}

.margin-y-sm {
  margin: .5em 0;
}

.margin-y-md {
  margin: 1em 0;
}

.margin-y-lg {
  margin: 1.5em 0;
}

.margin-y-xl {
  margin: 3em 0;
}

.margin-x-xs {
  margin: 0 .25em;
}

.margin-x-sm {
  margin: 0 .5em;
}

.margin-x-md {
  margin: 0 1em;
}

.margin-x-lg {
  margin: 0 1.5em;
}

.margin-x-xl {
  margin: 0 3em;
}

.margin-top-xs {
  margin-top: .25em;
}

.margin-top-sm {
  margin-top: .5em;
}

.margin-top-md {
  margin-top: 1em;
}

.margin-top-lg {
  margin-top: 1.5em;
}

.margin-top-xl {
  margin-top: 3em;
}

.margin-right-xs {
  margin-right: .25em;
}

.margin-right-sm {
  margin-right: .5em;
}

.margin-right-md {
  margin-right: 1em;
}

.margin-right-lg {
  margin-right: 1.5em;
}

.margin-right-xl {
  margin-right: 3em;
}

.margin-bottom-xs {
  margin-bottom: .25em;
}

.margin-bottom-sm {
  margin-bottom: .5em;
}

.margin-bottom-md {
  margin-bottom: 1em;
}

.margin-bottom-lg {
  margin-bottom: 1.5em;
}

.margin-bottom-xl {
  margin-bottom: 3em;
}

.margin-left-xs {
  margin-left: .25em;
}

.margin-left-sm {
  margin-left: .5em;
}

.margin-left-md {
  margin-left: 1em;
}

.margin-left-lg {
  margin-left: 1.5em;
}

.margin-left-xl {
  margin-left: 3em;
}
@media only screen and (max-width: 600px) {
    .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
    }
    .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
    }
}

/** RTL **/

.rtl {
    direction: rtl;
    font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
}

.rtl table {
    text-align: right;
}

.rtl table tr td:nth-child(2) {
    text-align: left;
}


.container {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
  }
  
  @media (min-width: 576px) {
    .container {
      max-width: 540px;
    }
  }
  
  @media (min-width: 768px) {
    .container {
      max-width: 720px;
    }
  }
  
  @media (min-width: 992px) {
    .container {
      max-width: 960px;
    }
  }
  
  @media (min-width: 1200px) {
    .container {
      max-width: 1140px;
    }
  }
  
  .container-fluid,
  .container-xl,
  .container-lg,
  .container-md,
  .container-sm {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
  }
  
  @media (min-width: 576px) {
    .container-sm,
    .container {
      max-width: 540px;
    }
  }
  
  @media (min-width: 768px) {
    .container-md,
    .container-sm,
    .container {
      max-width: 720px;
    }
  }
  
  @media (min-width: 992px) {
    .container-lg,
    .container-md,
    .container-sm,
    .container {
      max-width: 960px;
    }
  }
  
  @media (min-width: 1200px) {
    .container-xl,
    .container-lg,
    .container-md,
    .container-sm,
    .container {
      max-width: 1140px;
    }
  }
  
  .row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
  }
  
  .no-gutters {
    margin-right: 0;
    margin-left: 0;
  }
  
  .no-gutters > .col,
  .no-gutters > [class*=col-] {
    padding-right: 0;
    padding-left: 0;
  }
  
  .col-xl,
  .col-xl-auto,
  .col-xl-12,
  .col-xl-11,
  .col-xl-10,
  .col-xl-9,
  .col-xl-8,
  .col-xl-7,
  .col-xl-6,
  .col-xl-5,
  .col-xl-4,
  .col-xl-3,
  .col-xl-2,
  .col-xl-1,
  .col-lg,
  .col-lg-auto,
  .col-lg-12,
  .col-lg-11,
  .col-lg-10,
  .col-lg-9,
  .col-lg-8,
  .col-lg-7,
  .col-lg-6,
  .col-lg-5,
  .col-lg-4,
  .col-lg-3,
  .col-lg-2,
  .col-lg-1,
  .col-md,
  .col-md-auto,
  .col-md-12,
  .col-md-11,
  .col-md-10,
  .col-md-9,
  .col-md-8,
  .col-md-7,
  .col-md-6,
  .col-md-5,
  .col-md-4,
  .col-md-3,
  .col-md-2,
  .col-md-1,
  .col-sm,
  .col-sm-auto,
  .col-sm-12,
  .col-sm-11,
  .col-sm-10,
  .col-sm-9,
  .col-sm-8,
  .col-sm-7,
  .col-sm-6,
  .col-sm-5,
  .col-sm-4,
  .col-sm-3,
  .col-sm-2,
  .col-sm-1,
  .col,
  .col-auto,
  .col-12,
  .col-11,
  .col-10,
  .col-9,
  .col-8,
  .col-7,
  .col-6,
  .col-5,
  .col-4,
  .col-3,
  .col-2,
  .col-1 {
    position: relative;
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
  }
  
  .col {
    flex-basis: 0;
    flex-grow: 1;
    max-width: 100%;
  }
  
  .row-cols-1 > * {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .row-cols-2 > * {
    flex: 0 0 50%;
    max-width: 50%;
  }
  
  .row-cols-3 > * {
    flex: 0 0 33.3333333333%;
    max-width: 33.3333333333%;
  }
  
  .row-cols-4 > * {
    flex: 0 0 25%;
    max-width: 25%;
  }
  
  .row-cols-5 > * {
    flex: 0 0 20%;
    max-width: 20%;
  }
  
  .row-cols-6 > * {
    flex: 0 0 16.6666666667%;
    max-width: 16.6666666667%;
  }
  
  .col-auto {
    flex: 0 0 auto;
    width: auto;
    max-width: 100%;
  }
  
  .col-1 {
    flex: 0 0 8.3333333333%;
    max-width: 8.3333333333%;
  }
  
  .col-2 {
    flex: 0 0 16.6666666667%;
    max-width: 16.6666666667%;
  }
  
  .col-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
  
  .col-4 {
    flex: 0 0 33.3333333333%;
    max-width: 33.3333333333%;
  }
  
  .col-5 {
    flex: 0 0 41.6666666667%;
    max-width: 41.6666666667%;
  }
  
  .col-6 {
    flex: 0 0 50%;
    max-width: 50%;
  }
  
  .col-7 {
    flex: 0 0 58.3333333333%;
    max-width: 58.3333333333%;
  }
  
  .col-8 {
    flex: 0 0 66.6666666667%;
    max-width: 66.6666666667%;
  }
  
  .col-9 {
    flex: 0 0 75%;
    max-width: 75%;
  }
  
  .col-10 {
    flex: 0 0 83.3333333333%;
    max-width: 83.3333333333%;
  }
  
  .col-11 {
    flex: 0 0 91.6666666667%;
    max-width: 91.6666666667%;
  }
  
  .col-12 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .order-first {
    order: -1;
  }
  
  .order-last {
    order: 13;
  }
  
  .order-0 {
    order: 0;
  }
  
  .order-1 {
    order: 1;
  }
  
  .order-2 {
    order: 2;
  }
  
  .order-3 {
    order: 3;
  }
  
  .order-4 {
    order: 4;
  }
  
  .order-5 {
    order: 5;
  }
  
  .order-6 {
    order: 6;
  }
  
  .order-7 {
    order: 7;
  }
  
  .order-8 {
    order: 8;
  }
  
  .order-9 {
    order: 9;
  }
  
  .order-10 {
    order: 10;
  }
  
  .order-11 {
    order: 11;
  }
  
  .order-12 {
    order: 12;
  }
  
  .offset-1 {
    margin-left: 8.3333333333%;
  }
  
  .offset-2 {
    margin-left: 16.6666666667%;
  }
  
  .offset-3 {
    margin-left: 25%;
  }
  
  .offset-4 {
    margin-left: 33.3333333333%;
  }
  
  .offset-5 {
    margin-left: 41.6666666667%;
  }
  
  .offset-6 {
    margin-left: 50%;
  }
  
  .offset-7 {
    margin-left: 58.3333333333%;
  }
  
  .offset-8 {
    margin-left: 66.6666666667%;
  }
  
  .offset-9 {
    margin-left: 75%;
  }
  
  .offset-10 {
    margin-left: 83.3333333333%;
  }
  
  .offset-11 {
    margin-left: 91.6666666667%;
  }
  
  @media (min-width: 576px) {
    .col-sm {
      flex-basis: 0;
      flex-grow: 1;
      max-width: 100%;
    }
  
    .row-cols-sm-1 > * {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .row-cols-sm-2 > * {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .row-cols-sm-3 > * {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .row-cols-sm-4 > * {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .row-cols-sm-5 > * {
      flex: 0 0 20%;
      max-width: 20%;
    }
  
    .row-cols-sm-6 > * {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-sm-auto {
      flex: 0 0 auto;
      width: auto;
      max-width: 100%;
    }
  
    .col-sm-1 {
      flex: 0 0 8.3333333333%;
      max-width: 8.3333333333%;
    }
  
    .col-sm-2 {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-sm-3 {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .col-sm-4 {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .col-sm-5 {
      flex: 0 0 41.6666666667%;
      max-width: 41.6666666667%;
    }
  
    .col-sm-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .col-sm-7 {
      flex: 0 0 58.3333333333%;
      max-width: 58.3333333333%;
    }
  
    .col-sm-8 {
      flex: 0 0 66.6666666667%;
      max-width: 66.6666666667%;
    }
  
    .col-sm-9 {
      flex: 0 0 75%;
      max-width: 75%;
    }
  
    .col-sm-10 {
      flex: 0 0 83.3333333333%;
      max-width: 83.3333333333%;
    }
  
    .col-sm-11 {
      flex: 0 0 91.6666666667%;
      max-width: 91.6666666667%;
    }
  
    .col-sm-12 {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .order-sm-first {
      order: -1;
    }
  
    .order-sm-last {
      order: 13;
    }
  
    .order-sm-0 {
      order: 0;
    }
  
    .order-sm-1 {
      order: 1;
    }
  
    .order-sm-2 {
      order: 2;
    }
  
    .order-sm-3 {
      order: 3;
    }
  
    .order-sm-4 {
      order: 4;
    }
  
    .order-sm-5 {
      order: 5;
    }
  
    .order-sm-6 {
      order: 6;
    }
  
    .order-sm-7 {
      order: 7;
    }
  
    .order-sm-8 {
      order: 8;
    }
  
    .order-sm-9 {
      order: 9;
    }
  
    .order-sm-10 {
      order: 10;
    }
  
    .order-sm-11 {
      order: 11;
    }
  
    .order-sm-12 {
      order: 12;
    }
  
    .offset-sm-0 {
      margin-left: 0;
    }
  
    .offset-sm-1 {
      margin-left: 8.3333333333%;
    }
  
    .offset-sm-2 {
      margin-left: 16.6666666667%;
    }
  
    .offset-sm-3 {
      margin-left: 25%;
    }
  
    .offset-sm-4 {
      margin-left: 33.3333333333%;
    }
  
    .offset-sm-5 {
      margin-left: 41.6666666667%;
    }
  
    .offset-sm-6 {
      margin-left: 50%;
    }
  
    .offset-sm-7 {
      margin-left: 58.3333333333%;
    }
  
    .offset-sm-8 {
      margin-left: 66.6666666667%;
    }
  
    .offset-sm-9 {
      margin-left: 75%;
    }
  
    .offset-sm-10 {
      margin-left: 83.3333333333%;
    }
  
    .offset-sm-11 {
      margin-left: 91.6666666667%;
    }
  }
  
  @media (min-width: 768px) {
    .col-md {
      flex-basis: 0;
      flex-grow: 1;
      max-width: 100%;
    }
  
    .row-cols-md-1 > * {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .row-cols-md-2 > * {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .row-cols-md-3 > * {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .row-cols-md-4 > * {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .row-cols-md-5 > * {
      flex: 0 0 20%;
      max-width: 20%;
    }
  
    .row-cols-md-6 > * {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-md-auto {
      flex: 0 0 auto;
      width: auto;
      max-width: 100%;
    }
  
    .col-md-1 {
      flex: 0 0 8.3333333333%;
      max-width: 8.3333333333%;
    }
  
    .col-md-2 {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-md-3 {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .col-md-4 {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .col-md-5 {
      flex: 0 0 41.6666666667%;
      max-width: 41.6666666667%;
    }
  
    .col-md-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .col-md-7 {
      flex: 0 0 58.3333333333%;
      max-width: 58.3333333333%;
    }
  
    .col-md-8 {
      flex: 0 0 66.6666666667%;
      max-width: 66.6666666667%;
    }
  
    .col-md-9 {
      flex: 0 0 75%;
      max-width: 75%;
    }
  
    .col-md-10 {
      flex: 0 0 83.3333333333%;
      max-width: 83.3333333333%;
    }
  
    .col-md-11 {
      flex: 0 0 91.6666666667%;
      max-width: 91.6666666667%;
    }
  
    .col-md-12 {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .order-md-first {
      order: -1;
    }
  
    .order-md-last {
      order: 13;
    }
  
    .order-md-0 {
      order: 0;
    }
  
    .order-md-1 {
      order: 1;
    }
  
    .order-md-2 {
      order: 2;
    }
  
    .order-md-3 {
      order: 3;
    }
  
    .order-md-4 {
      order: 4;
    }
  
    .order-md-5 {
      order: 5;
    }
  
    .order-md-6 {
      order: 6;
    }
  
    .order-md-7 {
      order: 7;
    }
  
    .order-md-8 {
      order: 8;
    }
  
    .order-md-9 {
      order: 9;
    }
  
    .order-md-10 {
      order: 10;
    }
  
    .order-md-11 {
      order: 11;
    }
  
    .order-md-12 {
      order: 12;
    }
  
    .offset-md-0 {
      margin-left: 0;
    }
  
    .offset-md-1 {
      margin-left: 8.3333333333%;
    }
  
    .offset-md-2 {
      margin-left: 16.6666666667%;
    }
  
    .offset-md-3 {
      margin-left: 25%;
    }
  
    .offset-md-4 {
      margin-left: 33.3333333333%;
    }
  
    .offset-md-5 {
      margin-left: 41.6666666667%;
    }
  
    .offset-md-6 {
      margin-left: 50%;
    }
  
    .offset-md-7 {
      margin-left: 58.3333333333%;
    }
  
    .offset-md-8 {
      margin-left: 66.6666666667%;
    }
  
    .offset-md-9 {
      margin-left: 75%;
    }
  
    .offset-md-10 {
      margin-left: 83.3333333333%;
    }
  
    .offset-md-11 {
      margin-left: 91.6666666667%;
    }
  }
  
  @media (min-width: 992px) {
    .col-lg {
      flex-basis: 0;
      flex-grow: 1;
      max-width: 100%;
    }
  
    .row-cols-lg-1 > * {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .row-cols-lg-2 > * {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .row-cols-lg-3 > * {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .row-cols-lg-4 > * {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .row-cols-lg-5 > * {
      flex: 0 0 20%;
      max-width: 20%;
    }
  
    .row-cols-lg-6 > * {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-lg-auto {
      flex: 0 0 auto;
      width: auto;
      max-width: 100%;
    }
  
    .col-lg-1 {
      flex: 0 0 8.3333333333%;
      max-width: 8.3333333333%;
    }
  
    .col-lg-2 {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-lg-3 {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .col-lg-4 {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .col-lg-5 {
      flex: 0 0 41.6666666667%;
      max-width: 41.6666666667%;
    }
  
    .col-lg-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .col-lg-7 {
      flex: 0 0 58.3333333333%;
      max-width: 58.3333333333%;
    }
  
    .col-lg-8 {
      flex: 0 0 66.6666666667%;
      max-width: 66.6666666667%;
    }
  
    .col-lg-9 {
      flex: 0 0 75%;
      max-width: 75%;
    }
  
    .col-lg-10 {
      flex: 0 0 83.3333333333%;
      max-width: 83.3333333333%;
    }
  
    .col-lg-11 {
      flex: 0 0 91.6666666667%;
      max-width: 91.6666666667%;
    }
  
    .col-lg-12 {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .order-lg-first {
      order: -1;
    }
  
    .order-lg-last {
      order: 13;
    }
  
    .order-lg-0 {
      order: 0;
    }
  
    .order-lg-1 {
      order: 1;
    }
  
    .order-lg-2 {
      order: 2;
    }
  
    .order-lg-3 {
      order: 3;
    }
  
    .order-lg-4 {
      order: 4;
    }
  
    .order-lg-5 {
      order: 5;
    }
  
    .order-lg-6 {
      order: 6;
    }
  
    .order-lg-7 {
      order: 7;
    }
  
    .order-lg-8 {
      order: 8;
    }
  
    .order-lg-9 {
      order: 9;
    }
  
    .order-lg-10 {
      order: 10;
    }
  
    .order-lg-11 {
      order: 11;
    }
  
    .order-lg-12 {
      order: 12;
    }
  
    .offset-lg-0 {
      margin-left: 0;
    }
  
    .offset-lg-1 {
      margin-left: 8.3333333333%;
    }
  
    .offset-lg-2 {
      margin-left: 16.6666666667%;
    }
  
    .offset-lg-3 {
      margin-left: 25%;
    }
  
    .offset-lg-4 {
      margin-left: 33.3333333333%;
    }
  
    .offset-lg-5 {
      margin-left: 41.6666666667%;
    }
  
    .offset-lg-6 {
      margin-left: 50%;
    }
  
    .offset-lg-7 {
      margin-left: 58.3333333333%;
    }
  
    .offset-lg-8 {
      margin-left: 66.6666666667%;
    }
  
    .offset-lg-9 {
      margin-left: 75%;
    }
  
    .offset-lg-10 {
      margin-left: 83.3333333333%;
    }
  
    .offset-lg-11 {
      margin-left: 91.6666666667%;
    }
  }
  
  @media (min-width: 1200px) {
    .col-xl {
      flex-basis: 0;
      flex-grow: 1;
      max-width: 100%;
    }
  
    .row-cols-xl-1 > * {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .row-cols-xl-2 > * {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .row-cols-xl-3 > * {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .row-cols-xl-4 > * {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .row-cols-xl-5 > * {
      flex: 0 0 20%;
      max-width: 20%;
    }
  
    .row-cols-xl-6 > * {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-xl-auto {
      flex: 0 0 auto;
      width: auto;
      max-width: 100%;
    }
  
    .col-xl-1 {
      flex: 0 0 8.3333333333%;
      max-width: 8.3333333333%;
    }
  
    .col-xl-2 {
      flex: 0 0 16.6666666667%;
      max-width: 16.6666666667%;
    }
  
    .col-xl-3 {
      flex: 0 0 25%;
      max-width: 25%;
    }
  
    .col-xl-4 {
      flex: 0 0 33.3333333333%;
      max-width: 33.3333333333%;
    }
  
    .col-xl-5 {
      flex: 0 0 41.6666666667%;
      max-width: 41.6666666667%;
    }
  
    .col-xl-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  
    .col-xl-7 {
      flex: 0 0 58.3333333333%;
      max-width: 58.3333333333%;
    }
  
    .col-xl-8 {
      flex: 0 0 66.6666666667%;
      max-width: 66.6666666667%;
    }
  
    .col-xl-9 {
      flex: 0 0 75%;
      max-width: 75%;
    }
  
    .col-xl-10 {
      flex: 0 0 83.3333333333%;
      max-width: 83.3333333333%;
    }
  
    .col-xl-11 {
      flex: 0 0 91.6666666667%;
      max-width: 91.6666666667%;
    }
  
    .col-xl-12 {
      flex: 0 0 100%;
      max-width: 100%;
    }
  
    .order-xl-first {
      order: -1;
    }
  
    .order-xl-last {
      order: 13;
    }
  
    .order-xl-0 {
      order: 0;
    }
  
    .order-xl-1 {
      order: 1;
    }
  
    .order-xl-2 {
      order: 2;
    }
  
    .order-xl-3 {
      order: 3;
    }
  
    .order-xl-4 {
      order: 4;
    }
  
    .order-xl-5 {
      order: 5;
    }
  
    .order-xl-6 {
      order: 6;
    }
  
    .order-xl-7 {
      order: 7;
    }
  
    .order-xl-8 {
      order: 8;
    }
  
    .order-xl-9 {
      order: 9;
    }
  
    .order-xl-10 {
      order: 10;
    }
  
    .order-xl-11 {
      order: 11;
    }
  
    .order-xl-12 {
      order: 12;
    }
  
    .offset-xl-0 {
      margin-left: 0;
    }
  
    .offset-xl-1 {
      margin-left: 8.3333333333%;
    }
  
    .offset-xl-2 {
      margin-left: 16.6666666667%;
    }
  
    .offset-xl-3 {
      margin-left: 25%;
    }
  
    .offset-xl-4 {
      margin-left: 33.3333333333%;
    }
  
    .offset-xl-5 {
      margin-left: 41.6666666667%;
    }
  
    .offset-xl-6 {
      margin-left: 50%;
    }
  
    .offset-xl-7 {
      margin-left: 58.3333333333%;
    }
  
    .offset-xl-8 {
      margin-left: 66.6666666667%;
    }
  
    .offset-xl-9 {
      margin-left: 75%;
    }
  
    .offset-xl-10 {
      margin-left: 83.3333333333%;
    }
  
    .offset-xl-11 {
      margin-left: 91.6666666667%;
    }
  }

  .m-0 {
    margin: 0 !important;
  }
  
  .mt-0,
  .my-0 {
    margin-top: 0 !important;
  }
  
  .mr-0,
  .mx-0 {
    margin-right: 0 !important;
  }
  
  .mb-0,
  .my-0 {
    margin-bottom: 0 !important;
  }
  
  .ml-0,
  .mx-0 {
    margin-left: 0 !important;
  }
  
  .m-1 {
    margin: 0.25rem !important;
  }
  
  .mt-1,
  .my-1 {
    margin-top: 0.25rem !important;
  }
  
  .mr-1,
  .mx-1 {
    margin-right: 0.25rem !important;
  }
  
  .mb-1,
  .my-1 {
    margin-bottom: 0.25rem !important;
  }
  
  .ml-1,
  .mx-1 {
    margin-left: 0.25rem !important;
  }
  
  .m-2 {
    margin: 0.5rem !important;
  }
  
  .mt-2,
  .my-2 {
    margin-top: 0.5rem !important;
  }
  
  .mr-2,
  .mx-2 {
    margin-right: 0.5rem !important;
  }
  
  .mb-2,
  .my-2 {
    margin-bottom: 0.5rem !important;
  }
  
  .ml-2,
  .mx-2 {
    margin-left: 0.5rem !important;
  }
  
  .m-3 {
    margin: 1rem !important;
  }
  
  .mt-3,
  .my-3 {
    margin-top: 1rem !important;
  }
  
  .mr-3,
  .mx-3 {
    margin-right: 1rem !important;
  }
  
  .mb-3,
  .my-3 {
    margin-bottom: 1rem !important;
  }
  
  .ml-3,
  .mx-3 {
    margin-left: 1rem !important;
  }
  
  .m-4 {
    margin: 1.5rem !important;
  }
  
  .mt-4,
  .my-4 {
    margin-top: 1.5rem !important;
  }
  
  .mr-4,
  .mx-4 {
    margin-right: 1.5rem !important;
  }
  
  .mb-4,
  .my-4 {
    margin-bottom: 1.5rem !important;
  }
  
  .ml-4,
  .mx-4 {
    margin-left: 1.5rem !important;
  }
  
  .m-5 {
    margin: 3rem !important;
  }
  
  .mt-5,
  .my-5 {
    margin-top: 3rem !important;
  }
  
  .mr-5,
  .mx-5 {
    margin-right: 3rem !important;
  }
  
  .mb-5,
  .my-5 {
    margin-bottom: 3rem !important;
  }
  
  .ml-5,
  .mx-5 {
    margin-left: 3rem !important;
  }
  
  .p-0 {
    padding: 0 !important;
  }
  
  .pt-0,
  .py-0 {
    padding-top: 0 !important;
  }
  
  .pr-0,
  .px-0 {
    padding-right: 0 !important;
  }
  
  .pb-0,
  .py-0 {
    padding-bottom: 0 !important;
  }
  
  .pl-0,
  .px-0 {
    padding-left: 0 !important;
  }
  
  .p-1 {
    padding: 0.25rem !important;
  }
  
  .pt-1,
  .py-1 {
    padding-top: 0.25rem !important;
  }
  
  .pr-1,
  .px-1 {
    padding-right: 0.25rem !important;
  }
  
  .pb-1,
  .py-1 {
    padding-bottom: 0.25rem !important;
  }
  
  .pl-1,
  .px-1 {
    padding-left: 0.25rem !important;
  }
  
  .p-2 {
    padding: 0.5rem !important;
  }
  
  .pt-2,
  .py-2 {
    padding-top: 0.5rem !important;
  }
  
  .pr-2,
  .px-2 {
    padding-right: 0.5rem !important;
  }
  
  .pb-2,
  .py-2 {
    padding-bottom: 0.5rem !important;
  }
  
  .pl-2,
  .px-2 {
    padding-left: 0.5rem !important;
  }
  
  .p-3 {
    padding: 1rem !important;
  }
  
  .pt-3,
  .py-3 {
    padding-top: 1rem !important;
  }
  
  .pr-3,
  .px-3 {
    padding-right: 1rem !important;
  }
  
  .pb-3,
  .py-3 {
    padding-bottom: 1rem !important;
  }
  
  .pl-3,
  .px-3 {
    padding-left: 1rem !important;
  }
  
  .p-4 {
    padding: 1.5rem !important;
  }
  
  .pt-4,
  .py-4 {
    padding-top: 1.5rem !important;
  }
  
  .pr-4,
  .px-4 {
    padding-right: 1.5rem !important;
  }
  
  .pb-4,
  .py-4 {
    padding-bottom: 1.5rem !important;
  }
  
  .pl-4,
  .px-4 {
    padding-left: 1.5rem !important;
  }
  
  .p-5 {
    padding: 3rem !important;
  }
  
  .pt-5,
  .py-5 {
    padding-top: 3rem !important;
  }
  
  .pr-5,
  .px-5 {
    padding-right: 3rem !important;
  }
  
  .pb-5,
  .py-5 {
    padding-bottom: 3rem !important;
  }
  
  .pl-5,
  .px-5 {
    padding-left: 3rem !important;
  }
  
  .m-n1 {
    margin: -0.25rem !important;
  }
  
  .mt-n1,
  .my-n1 {
    margin-top: -0.25rem !important;
  }
  
  .mr-n1,
  .mx-n1 {
    margin-right: -0.25rem !important;
  }
  
  .mb-n1,
  .my-n1 {
    margin-bottom: -0.25rem !important;
  }
  
  .ml-n1,
  .mx-n1 {
    margin-left: -0.25rem !important;
  }
  
  .m-n2 {
    margin: -0.5rem !important;
  }
  
  .mt-n2,
  .my-n2 {
    margin-top: -0.5rem !important;
  }
  
  .mr-n2,
  .mx-n2 {
    margin-right: -0.5rem !important;
  }
  
  .mb-n2,
  .my-n2 {
    margin-bottom: -0.5rem !important;
  }
  
  .ml-n2,
  .mx-n2 {
    margin-left: -0.5rem !important;
  }
  
  .m-n3 {
    margin: -1rem !important;
  }
  
  .mt-n3,
  .my-n3 {
    margin-top: -1rem !important;
  }
  
  .mr-n3,
  .mx-n3 {
    margin-right: -1rem !important;
  }
  
  .mb-n3,
  .my-n3 {
    margin-bottom: -1rem !important;
  }
  
  .ml-n3,
  .mx-n3 {
    margin-left: -1rem !important;
  }
  
  .m-n4 {
    margin: -1.5rem !important;
  }
  
  .mt-n4,
  .my-n4 {
    margin-top: -1.5rem !important;
  }
  
  .mr-n4,
  .mx-n4 {
    margin-right: -1.5rem !important;
  }
  
  .mb-n4,
  .my-n4 {
    margin-bottom: -1.5rem !important;
  }
  
  .ml-n4,
  .mx-n4 {
    margin-left: -1.5rem !important;
  }
  
  .m-n5 {
    margin: -3rem !important;
  }
  
  .mt-n5,
  .my-n5 {
    margin-top: -3rem !important;
  }
  
  .mr-n5,
  .mx-n5 {
    margin-right: -3rem !important;
  }
  
  .mb-n5,
  .my-n5 {
    margin-bottom: -3rem !important;
  }
  
  .ml-n5,
  .mx-n5 {
    margin-left: -3rem !important;
  }
  
  .m-auto {
    margin: auto !important;
  }
  
  .mt-auto,
  .my-auto {
    margin-top: auto !important;
  }
  
  .mr-auto,
  .mx-auto {
    margin-right: auto !important;
  }
  
  .mb-auto,
  .my-auto {
    margin-bottom: auto !important;
  }
  
  .ml-auto,
  .mx-auto {
    margin-left: auto !important;
  }
  
  @media (min-width: 576px) {
    .m-sm-0 {
      margin: 0 !important;
    }
  
    .mt-sm-0,
    .my-sm-0 {
      margin-top: 0 !important;
    }
  
    .mr-sm-0,
    .mx-sm-0 {
      margin-right: 0 !important;
    }
  
    .mb-sm-0,
    .my-sm-0 {
      margin-bottom: 0 !important;
    }
  
    .ml-sm-0,
    .mx-sm-0 {
      margin-left: 0 !important;
    }
  
    .m-sm-1 {
      margin: 0.25rem !important;
    }
  
    .mt-sm-1,
    .my-sm-1 {
      margin-top: 0.25rem !important;
    }
  
    .mr-sm-1,
    .mx-sm-1 {
      margin-right: 0.25rem !important;
    }
  
    .mb-sm-1,
    .my-sm-1 {
      margin-bottom: 0.25rem !important;
    }
  
    .ml-sm-1,
    .mx-sm-1 {
      margin-left: 0.25rem !important;
    }
  
    .m-sm-2 {
      margin: 0.5rem !important;
    }
  
    .mt-sm-2,
    .my-sm-2 {
      margin-top: 0.5rem !important;
    }
  
    .mr-sm-2,
    .mx-sm-2 {
      margin-right: 0.5rem !important;
    }
  
    .mb-sm-2,
    .my-sm-2 {
      margin-bottom: 0.5rem !important;
    }
  
    .ml-sm-2,
    .mx-sm-2 {
      margin-left: 0.5rem !important;
    }
  
    .m-sm-3 {
      margin: 1rem !important;
    }
  
    .mt-sm-3,
    .my-sm-3 {
      margin-top: 1rem !important;
    }
  
    .mr-sm-3,
    .mx-sm-3 {
      margin-right: 1rem !important;
    }
  
    .mb-sm-3,
    .my-sm-3 {
      margin-bottom: 1rem !important;
    }
  
    .ml-sm-3,
    .mx-sm-3 {
      margin-left: 1rem !important;
    }
  
    .m-sm-4 {
      margin: 1.5rem !important;
    }
  
    .mt-sm-4,
    .my-sm-4 {
      margin-top: 1.5rem !important;
    }
  
    .mr-sm-4,
    .mx-sm-4 {
      margin-right: 1.5rem !important;
    }
  
    .mb-sm-4,
    .my-sm-4 {
      margin-bottom: 1.5rem !important;
    }
  
    .ml-sm-4,
    .mx-sm-4 {
      margin-left: 1.5rem !important;
    }
  
    .m-sm-5 {
      margin: 3rem !important;
    }
  
    .mt-sm-5,
    .my-sm-5 {
      margin-top: 3rem !important;
    }
  
    .mr-sm-5,
    .mx-sm-5 {
      margin-right: 3rem !important;
    }
  
    .mb-sm-5,
    .my-sm-5 {
      margin-bottom: 3rem !important;
    }
  
    .ml-sm-5,
    .mx-sm-5 {
      margin-left: 3rem !important;
    }
  
    .p-sm-0 {
      padding: 0 !important;
    }
  
    .pt-sm-0,
    .py-sm-0 {
      padding-top: 0 !important;
    }
  
    .pr-sm-0,
    .px-sm-0 {
      padding-right: 0 !important;
    }
  
    .pb-sm-0,
    .py-sm-0 {
      padding-bottom: 0 !important;
    }
  
    .pl-sm-0,
    .px-sm-0 {
      padding-left: 0 !important;
    }
  
    .p-sm-1 {
      padding: 0.25rem !important;
    }
  
    .pt-sm-1,
    .py-sm-1 {
      padding-top: 0.25rem !important;
    }
  
    .pr-sm-1,
    .px-sm-1 {
      padding-right: 0.25rem !important;
    }
  
    .pb-sm-1,
    .py-sm-1 {
      padding-bottom: 0.25rem !important;
    }
  
    .pl-sm-1,
    .px-sm-1 {
      padding-left: 0.25rem !important;
    }
  
    .p-sm-2 {
      padding: 0.5rem !important;
    }
  
    .pt-sm-2,
    .py-sm-2 {
      padding-top: 0.5rem !important;
    }
  
    .pr-sm-2,
    .px-sm-2 {
      padding-right: 0.5rem !important;
    }
  
    .pb-sm-2,
    .py-sm-2 {
      padding-bottom: 0.5rem !important;
    }
  
    .pl-sm-2,
    .px-sm-2 {
      padding-left: 0.5rem !important;
    }
  
    .p-sm-3 {
      padding: 1rem !important;
    }
  
    .pt-sm-3,
    .py-sm-3 {
      padding-top: 1rem !important;
    }
  
    .pr-sm-3,
    .px-sm-3 {
      padding-right: 1rem !important;
    }
  
    .pb-sm-3,
    .py-sm-3 {
      padding-bottom: 1rem !important;
    }
  
    .pl-sm-3,
    .px-sm-3 {
      padding-left: 1rem !important;
    }
  
    .p-sm-4 {
      padding: 1.5rem !important;
    }
  
    .pt-sm-4,
    .py-sm-4 {
      padding-top: 1.5rem !important;
    }
  
    .pr-sm-4,
    .px-sm-4 {
      padding-right: 1.5rem !important;
    }
  
    .pb-sm-4,
    .py-sm-4 {
      padding-bottom: 1.5rem !important;
    }
  
    .pl-sm-4,
    .px-sm-4 {
      padding-left: 1.5rem !important;
    }
  
    .p-sm-5 {
      padding: 3rem !important;
    }
  
    .pt-sm-5,
    .py-sm-5 {
      padding-top: 3rem !important;
    }
  
    .pr-sm-5,
    .px-sm-5 {
      padding-right: 3rem !important;
    }
  
    .pb-sm-5,
    .py-sm-5 {
      padding-bottom: 3rem !important;
    }
  
    .pl-sm-5,
    .px-sm-5 {
      padding-left: 3rem !important;
    }
  
    .m-sm-n1 {
      margin: -0.25rem !important;
    }
  
    .mt-sm-n1,
    .my-sm-n1 {
      margin-top: -0.25rem !important;
    }
  
    .mr-sm-n1,
    .mx-sm-n1 {
      margin-right: -0.25rem !important;
    }
  
    .mb-sm-n1,
    .my-sm-n1 {
      margin-bottom: -0.25rem !important;
    }
  
    .ml-sm-n1,
    .mx-sm-n1 {
      margin-left: -0.25rem !important;
    }
  
    .m-sm-n2 {
      margin: -0.5rem !important;
    }
  
    .mt-sm-n2,
    .my-sm-n2 {
      margin-top: -0.5rem !important;
    }
  
    .mr-sm-n2,
    .mx-sm-n2 {
      margin-right: -0.5rem !important;
    }
  
    .mb-sm-n2,
    .my-sm-n2 {
      margin-bottom: -0.5rem !important;
    }
  
    .ml-sm-n2,
    .mx-sm-n2 {
      margin-left: -0.5rem !important;
    }
  
    .m-sm-n3 {
      margin: -1rem !important;
    }
  
    .mt-sm-n3,
    .my-sm-n3 {
      margin-top: -1rem !important;
    }
  
    .mr-sm-n3,
    .mx-sm-n3 {
      margin-right: -1rem !important;
    }
  
    .mb-sm-n3,
    .my-sm-n3 {
      margin-bottom: -1rem !important;
    }
  
    .ml-sm-n3,
    .mx-sm-n3 {
      margin-left: -1rem !important;
    }
  
    .m-sm-n4 {
      margin: -1.5rem !important;
    }
  
    .mt-sm-n4,
    .my-sm-n4 {
      margin-top: -1.5rem !important;
    }
  
    .mr-sm-n4,
    .mx-sm-n4 {
      margin-right: -1.5rem !important;
    }
  
    .mb-sm-n4,
    .my-sm-n4 {
      margin-bottom: -1.5rem !important;
    }
  
    .ml-sm-n4,
    .mx-sm-n4 {
      margin-left: -1.5rem !important;
    }
  
    .m-sm-n5 {
      margin: -3rem !important;
    }
  
    .mt-sm-n5,
    .my-sm-n5 {
      margin-top: -3rem !important;
    }
  
    .mr-sm-n5,
    .mx-sm-n5 {
      margin-right: -3rem !important;
    }
  
    .mb-sm-n5,
    .my-sm-n5 {
      margin-bottom: -3rem !important;
    }
  
    .ml-sm-n5,
    .mx-sm-n5 {
      margin-left: -3rem !important;
    }
  
    .m-sm-auto {
      margin: auto !important;
    }
  
    .mt-sm-auto,
    .my-sm-auto {
      margin-top: auto !important;
    }
  
    .mr-sm-auto,
    .mx-sm-auto {
      margin-right: auto !important;
    }
  
    .mb-sm-auto,
    .my-sm-auto {
      margin-bottom: auto !important;
    }
  
    .ml-sm-auto,
    .mx-sm-auto {
      margin-left: auto !important;
    }
  }
  
  @media (min-width: 768px) {
    .m-md-0 {
      margin: 0 !important;
    }
  
    .mt-md-0,
    .my-md-0 {
      margin-top: 0 !important;
    }
  
    .mr-md-0,
    .mx-md-0 {
      margin-right: 0 !important;
    }
  
    .mb-md-0,
    .my-md-0 {
      margin-bottom: 0 !important;
    }
  
    .ml-md-0,
    .mx-md-0 {
      margin-left: 0 !important;
    }
  
    .m-md-1 {
      margin: 0.25rem !important;
    }
  
    .mt-md-1,
    .my-md-1 {
      margin-top: 0.25rem !important;
    }
  
    .mr-md-1,
    .mx-md-1 {
      margin-right: 0.25rem !important;
    }
  
    .mb-md-1,
    .my-md-1 {
      margin-bottom: 0.25rem !important;
    }
  
    .ml-md-1,
    .mx-md-1 {
      margin-left: 0.25rem !important;
    }
  
    .m-md-2 {
      margin: 0.5rem !important;
    }
  
    .mt-md-2,
    .my-md-2 {
      margin-top: 0.5rem !important;
    }
  
    .mr-md-2,
    .mx-md-2 {
      margin-right: 0.5rem !important;
    }
  
    .mb-md-2,
    .my-md-2 {
      margin-bottom: 0.5rem !important;
    }
  
    .ml-md-2,
    .mx-md-2 {
      margin-left: 0.5rem !important;
    }
  
    .m-md-3 {
      margin: 1rem !important;
    }
  
    .mt-md-3,
    .my-md-3 {
      margin-top: 1rem !important;
    }
  
    .mr-md-3,
    .mx-md-3 {
      margin-right: 1rem !important;
    }
  
    .mb-md-3,
    .my-md-3 {
      margin-bottom: 1rem !important;
    }
  
    .ml-md-3,
    .mx-md-3 {
      margin-left: 1rem !important;
    }
  
    .m-md-4 {
      margin: 1.5rem !important;
    }
  
    .mt-md-4,
    .my-md-4 {
      margin-top: 1.5rem !important;
    }
  
    .mr-md-4,
    .mx-md-4 {
      margin-right: 1.5rem !important;
    }
  
    .mb-md-4,
    .my-md-4 {
      margin-bottom: 1.5rem !important;
    }
  
    .ml-md-4,
    .mx-md-4 {
      margin-left: 1.5rem !important;
    }
  
    .m-md-5 {
      margin: 3rem !important;
    }
  
    .mt-md-5,
    .my-md-5 {
      margin-top: 3rem !important;
    }
  
    .mr-md-5,
    .mx-md-5 {
      margin-right: 3rem !important;
    }
  
    .mb-md-5,
    .my-md-5 {
      margin-bottom: 3rem !important;
    }
  
    .ml-md-5,
    .mx-md-5 {
      margin-left: 3rem !important;
    }
  
    .p-md-0 {
      padding: 0 !important;
    }
  
    .pt-md-0,
    .py-md-0 {
      padding-top: 0 !important;
    }
  
    .pr-md-0,
    .px-md-0 {
      padding-right: 0 !important;
    }
  
    .pb-md-0,
    .py-md-0 {
      padding-bottom: 0 !important;
    }
  
    .pl-md-0,
    .px-md-0 {
      padding-left: 0 !important;
    }
  
    .p-md-1 {
      padding: 0.25rem !important;
    }
  
    .pt-md-1,
    .py-md-1 {
      padding-top: 0.25rem !important;
    }
  
    .pr-md-1,
    .px-md-1 {
      padding-right: 0.25rem !important;
    }
  
    .pb-md-1,
    .py-md-1 {
      padding-bottom: 0.25rem !important;
    }
  
    .pl-md-1,
    .px-md-1 {
      padding-left: 0.25rem !important;
    }
  
    .p-md-2 {
      padding: 0.5rem !important;
    }
  
    .pt-md-2,
    .py-md-2 {
      padding-top: 0.5rem !important;
    }
  
    .pr-md-2,
    .px-md-2 {
      padding-right: 0.5rem !important;
    }
  
    .pb-md-2,
    .py-md-2 {
      padding-bottom: 0.5rem !important;
    }
  
    .pl-md-2,
    .px-md-2 {
      padding-left: 0.5rem !important;
    }
  
    .p-md-3 {
      padding: 1rem !important;
    }
  
    .pt-md-3,
    .py-md-3 {
      padding-top: 1rem !important;
    }
  
    .pr-md-3,
    .px-md-3 {
      padding-right: 1rem !important;
    }
  
    .pb-md-3,
    .py-md-3 {
      padding-bottom: 1rem !important;
    }
  
    .pl-md-3,
    .px-md-3 {
      padding-left: 1rem !important;
    }
  
    .p-md-4 {
      padding: 1.5rem !important;
    }
  
    .pt-md-4,
    .py-md-4 {
      padding-top: 1.5rem !important;
    }
  
    .pr-md-4,
    .px-md-4 {
      padding-right: 1.5rem !important;
    }
  
    .pb-md-4,
    .py-md-4 {
      padding-bottom: 1.5rem !important;
    }
  
    .pl-md-4,
    .px-md-4 {
      padding-left: 1.5rem !important;
    }
  
    .p-md-5 {
      padding: 3rem !important;
    }
  
    .pt-md-5,
    .py-md-5 {
      padding-top: 3rem !important;
    }
  
    .pr-md-5,
    .px-md-5 {
      padding-right: 3rem !important;
    }
  
    .pb-md-5,
    .py-md-5 {
      padding-bottom: 3rem !important;
    }
  
    .pl-md-5,
    .px-md-5 {
      padding-left: 3rem !important;
    }
  
    .m-md-n1 {
      margin: -0.25rem !important;
    }
  
    .mt-md-n1,
    .my-md-n1 {
      margin-top: -0.25rem !important;
    }
  
    .mr-md-n1,
    .mx-md-n1 {
      margin-right: -0.25rem !important;
    }
  
    .mb-md-n1,
    .my-md-n1 {
      margin-bottom: -0.25rem !important;
    }
  
    .ml-md-n1,
    .mx-md-n1 {
      margin-left: -0.25rem !important;
    }
  
    .m-md-n2 {
      margin: -0.5rem !important;
    }
  
    .mt-md-n2,
    .my-md-n2 {
      margin-top: -0.5rem !important;
    }
  
    .mr-md-n2,
    .mx-md-n2 {
      margin-right: -0.5rem !important;
    }
  
    .mb-md-n2,
    .my-md-n2 {
      margin-bottom: -0.5rem !important;
    }
  
    .ml-md-n2,
    .mx-md-n2 {
      margin-left: -0.5rem !important;
    }
  
    .m-md-n3 {
      margin: -1rem !important;
    }
  
    .mt-md-n3,
    .my-md-n3 {
      margin-top: -1rem !important;
    }
  
    .mr-md-n3,
    .mx-md-n3 {
      margin-right: -1rem !important;
    }
  
    .mb-md-n3,
    .my-md-n3 {
      margin-bottom: -1rem !important;
    }
  
    .ml-md-n3,
    .mx-md-n3 {
      margin-left: -1rem !important;
    }
  
    .m-md-n4 {
      margin: -1.5rem !important;
    }
  
    .mt-md-n4,
    .my-md-n4 {
      margin-top: -1.5rem !important;
    }
  
    .mr-md-n4,
    .mx-md-n4 {
      margin-right: -1.5rem !important;
    }
  
    .mb-md-n4,
    .my-md-n4 {
      margin-bottom: -1.5rem !important;
    }
  
    .ml-md-n4,
    .mx-md-n4 {
      margin-left: -1.5rem !important;
    }
  
    .m-md-n5 {
      margin: -3rem !important;
    }
  
    .mt-md-n5,
    .my-md-n5 {
      margin-top: -3rem !important;
    }
  
    .mr-md-n5,
    .mx-md-n5 {
      margin-right: -3rem !important;
    }
  
    .mb-md-n5,
    .my-md-n5 {
      margin-bottom: -3rem !important;
    }
  
    .ml-md-n5,
    .mx-md-n5 {
      margin-left: -3rem !important;
    }
  
    .m-md-auto {
      margin: auto !important;
    }
  
    .mt-md-auto,
    .my-md-auto {
      margin-top: auto !important;
    }
  
    .mr-md-auto,
    .mx-md-auto {
      margin-right: auto !important;
    }
  
    .mb-md-auto,
    .my-md-auto {
      margin-bottom: auto !important;
    }
  
    .ml-md-auto,
    .mx-md-auto {
      margin-left: auto !important;
    }
  }
  
  @media (min-width: 992px) {
    .m-lg-0 {
      margin: 0 !important;
    }
  
    .mt-lg-0,
    .my-lg-0 {
      margin-top: 0 !important;
    }
  
    .mr-lg-0,
    .mx-lg-0 {
      margin-right: 0 !important;
    }
  
    .mb-lg-0,
    .my-lg-0 {
      margin-bottom: 0 !important;
    }
  
    .ml-lg-0,
    .mx-lg-0 {
      margin-left: 0 !important;
    }
  
    .m-lg-1 {
      margin: 0.25rem !important;
    }
  
    .mt-lg-1,
    .my-lg-1 {
      margin-top: 0.25rem !important;
    }
  
    .mr-lg-1,
    .mx-lg-1 {
      margin-right: 0.25rem !important;
    }
  
    .mb-lg-1,
    .my-lg-1 {
      margin-bottom: 0.25rem !important;
    }
  
    .ml-lg-1,
    .mx-lg-1 {
      margin-left: 0.25rem !important;
    }
  
    .m-lg-2 {
      margin: 0.5rem !important;
    }
  
    .mt-lg-2,
    .my-lg-2 {
      margin-top: 0.5rem !important;
    }
  
    .mr-lg-2,
    .mx-lg-2 {
      margin-right: 0.5rem !important;
    }
  
    .mb-lg-2,
    .my-lg-2 {
      margin-bottom: 0.5rem !important;
    }
  
    .ml-lg-2,
    .mx-lg-2 {
      margin-left: 0.5rem !important;
    }
  
    .m-lg-3 {
      margin: 1rem !important;
    }
  
    .mt-lg-3,
    .my-lg-3 {
      margin-top: 1rem !important;
    }
  
    .mr-lg-3,
    .mx-lg-3 {
      margin-right: 1rem !important;
    }
  
    .mb-lg-3,
    .my-lg-3 {
      margin-bottom: 1rem !important;
    }
  
    .ml-lg-3,
    .mx-lg-3 {
      margin-left: 1rem !important;
    }
  
    .m-lg-4 {
      margin: 1.5rem !important;
    }
  
    .mt-lg-4,
    .my-lg-4 {
      margin-top: 1.5rem !important;
    }
  
    .mr-lg-4,
    .mx-lg-4 {
      margin-right: 1.5rem !important;
    }
  
    .mb-lg-4,
    .my-lg-4 {
      margin-bottom: 1.5rem !important;
    }
  
    .ml-lg-4,
    .mx-lg-4 {
      margin-left: 1.5rem !important;
    }
  
    .m-lg-5 {
      margin: 3rem !important;
    }
  
    .mt-lg-5,
    .my-lg-5 {
      margin-top: 3rem !important;
    }
  
    .mr-lg-5,
    .mx-lg-5 {
      margin-right: 3rem !important;
    }
  
    .mb-lg-5,
    .my-lg-5 {
      margin-bottom: 3rem !important;
    }
  
    .ml-lg-5,
    .mx-lg-5 {
      margin-left: 3rem !important;
    }
  
    .p-lg-0 {
      padding: 0 !important;
    }
  
    .pt-lg-0,
    .py-lg-0 {
      padding-top: 0 !important;
    }
  
    .pr-lg-0,
    .px-lg-0 {
      padding-right: 0 !important;
    }
  
    .pb-lg-0,
    .py-lg-0 {
      padding-bottom: 0 !important;
    }
  
    .pl-lg-0,
    .px-lg-0 {
      padding-left: 0 !important;
    }
  
    .p-lg-1 {
      padding: 0.25rem !important;
    }
  
    .pt-lg-1,
    .py-lg-1 {
      padding-top: 0.25rem !important;
    }
  
    .pr-lg-1,
    .px-lg-1 {
      padding-right: 0.25rem !important;
    }
  
    .pb-lg-1,
    .py-lg-1 {
      padding-bottom: 0.25rem !important;
    }
  
    .pl-lg-1,
    .px-lg-1 {
      padding-left: 0.25rem !important;
    }
  
    .p-lg-2 {
      padding: 0.5rem !important;
    }
  
    .pt-lg-2,
    .py-lg-2 {
      padding-top: 0.5rem !important;
    }
  
    .pr-lg-2,
    .px-lg-2 {
      padding-right: 0.5rem !important;
    }
  
    .pb-lg-2,
    .py-lg-2 {
      padding-bottom: 0.5rem !important;
    }
  
    .pl-lg-2,
    .px-lg-2 {
      padding-left: 0.5rem !important;
    }
  
    .p-lg-3 {
      padding: 1rem !important;
    }
  
    .pt-lg-3,
    .py-lg-3 {
      padding-top: 1rem !important;
    }
  
    .pr-lg-3,
    .px-lg-3 {
      padding-right: 1rem !important;
    }
  
    .pb-lg-3,
    .py-lg-3 {
      padding-bottom: 1rem !important;
    }
  
    .pl-lg-3,
    .px-lg-3 {
      padding-left: 1rem !important;
    }
  
    .p-lg-4 {
      padding: 1.5rem !important;
    }
  
    .pt-lg-4,
    .py-lg-4 {
      padding-top: 1.5rem !important;
    }
  
    .pr-lg-4,
    .px-lg-4 {
      padding-right: 1.5rem !important;
    }
  
    .pb-lg-4,
    .py-lg-4 {
      padding-bottom: 1.5rem !important;
    }
  
    .pl-lg-4,
    .px-lg-4 {
      padding-left: 1.5rem !important;
    }
  
    .p-lg-5 {
      padding: 3rem !important;
    }
  
    .pt-lg-5,
    .py-lg-5 {
      padding-top: 3rem !important;
    }
  
    .pr-lg-5,
    .px-lg-5 {
      padding-right: 3rem !important;
    }
  
    .pb-lg-5,
    .py-lg-5 {
      padding-bottom: 3rem !important;
    }
  
    .pl-lg-5,
    .px-lg-5 {
      padding-left: 3rem !important;
    }
  
    .m-lg-n1 {
      margin: -0.25rem !important;
    }
  
    .mt-lg-n1,
    .my-lg-n1 {
      margin-top: -0.25rem !important;
    }
  
    .mr-lg-n1,
    .mx-lg-n1 {
      margin-right: -0.25rem !important;
    }
  
    .mb-lg-n1,
    .my-lg-n1 {
      margin-bottom: -0.25rem !important;
    }
  
    .ml-lg-n1,
    .mx-lg-n1 {
      margin-left: -0.25rem !important;
    }
  
    .m-lg-n2 {
      margin: -0.5rem !important;
    }
  
    .mt-lg-n2,
    .my-lg-n2 {
      margin-top: -0.5rem !important;
    }
  
    .mr-lg-n2,
    .mx-lg-n2 {
      margin-right: -0.5rem !important;
    }
  
    .mb-lg-n2,
    .my-lg-n2 {
      margin-bottom: -0.5rem !important;
    }
  
    .ml-lg-n2,
    .mx-lg-n2 {
      margin-left: -0.5rem !important;
    }
  
    .m-lg-n3 {
      margin: -1rem !important;
    }
  
    .mt-lg-n3,
    .my-lg-n3 {
      margin-top: -1rem !important;
    }
  
    .mr-lg-n3,
    .mx-lg-n3 {
      margin-right: -1rem !important;
    }
  
    .mb-lg-n3,
    .my-lg-n3 {
      margin-bottom: -1rem !important;
    }
  
    .ml-lg-n3,
    .mx-lg-n3 {
      margin-left: -1rem !important;
    }
  
    .m-lg-n4 {
      margin: -1.5rem !important;
    }
  
    .mt-lg-n4,
    .my-lg-n4 {
      margin-top: -1.5rem !important;
    }
  
    .mr-lg-n4,
    .mx-lg-n4 {
      margin-right: -1.5rem !important;
    }
  
    .mb-lg-n4,
    .my-lg-n4 {
      margin-bottom: -1.5rem !important;
    }
  
    .ml-lg-n4,
    .mx-lg-n4 {
      margin-left: -1.5rem !important;
    }
  
    .m-lg-n5 {
      margin: -3rem !important;
    }
  
    .mt-lg-n5,
    .my-lg-n5 {
      margin-top: -3rem !important;
    }
  
    .mr-lg-n5,
    .mx-lg-n5 {
      margin-right: -3rem !important;
    }
  
    .mb-lg-n5,
    .my-lg-n5 {
      margin-bottom: -3rem !important;
    }
  
    .ml-lg-n5,
    .mx-lg-n5 {
      margin-left: -3rem !important;
    }
  
    .m-lg-auto {
      margin: auto !important;
    }
  
    .mt-lg-auto,
    .my-lg-auto {
      margin-top: auto !important;
    }
  
    .mr-lg-auto,
    .mx-lg-auto {
      margin-right: auto !important;
    }
  
    .mb-lg-auto,
    .my-lg-auto {
      margin-bottom: auto !important;
    }
  
    .ml-lg-auto,
    .mx-lg-auto {
      margin-left: auto !important;
    }
  }
  
  @media (min-width: 1200px) {
    .m-xl-0 {
      margin: 0 !important;
    }
  
    .mt-xl-0,
    .my-xl-0 {
      margin-top: 0 !important;
    }
  
    .mr-xl-0,
    .mx-xl-0 {
      margin-right: 0 !important;
    }
  
    .mb-xl-0,
    .my-xl-0 {
      margin-bottom: 0 !important;
    }
  
    .ml-xl-0,
    .mx-xl-0 {
      margin-left: 0 !important;
    }
  
    .m-xl-1 {
      margin: 0.25rem !important;
    }
  
    .mt-xl-1,
    .my-xl-1 {
      margin-top: 0.25rem !important;
    }
  
    .mr-xl-1,
    .mx-xl-1 {
      margin-right: 0.25rem !important;
    }
  
    .mb-xl-1,
    .my-xl-1 {
      margin-bottom: 0.25rem !important;
    }
  
    .ml-xl-1,
    .mx-xl-1 {
      margin-left: 0.25rem !important;
    }
  
    .m-xl-2 {
      margin: 0.5rem !important;
    }
  
    .mt-xl-2,
    .my-xl-2 {
      margin-top: 0.5rem !important;
    }
  
    .mr-xl-2,
    .mx-xl-2 {
      margin-right: 0.5rem !important;
    }
  
    .mb-xl-2,
    .my-xl-2 {
      margin-bottom: 0.5rem !important;
    }
  
    .ml-xl-2,
    .mx-xl-2 {
      margin-left: 0.5rem !important;
    }
  
    .m-xl-3 {
      margin: 1rem !important;
    }
  
    .mt-xl-3,
    .my-xl-3 {
      margin-top: 1rem !important;
    }
  
    .mr-xl-3,
    .mx-xl-3 {
      margin-right: 1rem !important;
    }
  
    .mb-xl-3,
    .my-xl-3 {
      margin-bottom: 1rem !important;
    }
  
    .ml-xl-3,
    .mx-xl-3 {
      margin-left: 1rem !important;
    }
  
    .m-xl-4 {
      margin: 1.5rem !important;
    }
  
    .mt-xl-4,
    .my-xl-4 {
      margin-top: 1.5rem !important;
    }
  
    .mr-xl-4,
    .mx-xl-4 {
      margin-right: 1.5rem !important;
    }
  
    .mb-xl-4,
    .my-xl-4 {
      margin-bottom: 1.5rem !important;
    }
  
    .ml-xl-4,
    .mx-xl-4 {
      margin-left: 1.5rem !important;
    }
  
    .m-xl-5 {
      margin: 3rem !important;
    }
  
    .mt-xl-5,
    .my-xl-5 {
      margin-top: 3rem !important;
    }
  
    .mr-xl-5,
    .mx-xl-5 {
      margin-right: 3rem !important;
    }
  
    .mb-xl-5,
    .my-xl-5 {
      margin-bottom: 3rem !important;
    }
  
    .ml-xl-5,
    .mx-xl-5 {
      margin-left: 3rem !important;
    }
  
    .p-xl-0 {
      padding: 0 !important;
    }
  
    .pt-xl-0,
    .py-xl-0 {
      padding-top: 0 !important;
    }
  
    .pr-xl-0,
    .px-xl-0 {
      padding-right: 0 !important;
    }
  
    .pb-xl-0,
    .py-xl-0 {
      padding-bottom: 0 !important;
    }
  
    .pl-xl-0,
    .px-xl-0 {
      padding-left: 0 !important;
    }
  
    .p-xl-1 {
      padding: 0.25rem !important;
    }
  
    .pt-xl-1,
    .py-xl-1 {
      padding-top: 0.25rem !important;
    }
  
    .pr-xl-1,
    .px-xl-1 {
      padding-right: 0.25rem !important;
    }
  
    .pb-xl-1,
    .py-xl-1 {
      padding-bottom: 0.25rem !important;
    }
  
    .pl-xl-1,
    .px-xl-1 {
      padding-left: 0.25rem !important;
    }
  
    .p-xl-2 {
      padding: 0.5rem !important;
    }
  
    .pt-xl-2,
    .py-xl-2 {
      padding-top: 0.5rem !important;
    }
  
    .pr-xl-2,
    .px-xl-2 {
      padding-right: 0.5rem !important;
    }
  
    .pb-xl-2,
    .py-xl-2 {
      padding-bottom: 0.5rem !important;
    }
  
    .pl-xl-2,
    .px-xl-2 {
      padding-left: 0.5rem !important;
    }
  
    .p-xl-3 {
      padding: 1rem !important;
    }
  
    .pt-xl-3,
    .py-xl-3 {
      padding-top: 1rem !important;
    }
  
    .pr-xl-3,
    .px-xl-3 {
      padding-right: 1rem !important;
    }
  
    .pb-xl-3,
    .py-xl-3 {
      padding-bottom: 1rem !important;
    }
  
    .pl-xl-3,
    .px-xl-3 {
      padding-left: 1rem !important;
    }
  
    .p-xl-4 {
      padding: 1.5rem !important;
    }
  
    .pt-xl-4,
    .py-xl-4 {
      padding-top: 1.5rem !important;
    }
  
    .pr-xl-4,
    .px-xl-4 {
      padding-right: 1.5rem !important;
    }
  
    .pb-xl-4,
    .py-xl-4 {
      padding-bottom: 1.5rem !important;
    }
  
    .pl-xl-4,
    .px-xl-4 {
      padding-left: 1.5rem !important;
    }
  
    .p-xl-5 {
      padding: 3rem !important;
    }
  
    .pt-xl-5,
    .py-xl-5 {
      padding-top: 3rem !important;
    }
  
    .pr-xl-5,
    .px-xl-5 {
      padding-right: 3rem !important;
    }
  
    .pb-xl-5,
    .py-xl-5 {
      padding-bottom: 3rem !important;
    }
  
    .pl-xl-5,
    .px-xl-5 {
      padding-left: 3rem !important;
    }
  
    .m-xl-n1 {
      margin: -0.25rem !important;
    }
  
    .mt-xl-n1,
    .my-xl-n1 {
      margin-top: -0.25rem !important;
    }
  
    .mr-xl-n1,
    .mx-xl-n1 {
      margin-right: -0.25rem !important;
    }
  
    .mb-xl-n1,
    .my-xl-n1 {
      margin-bottom: -0.25rem !important;
    }
  
    .ml-xl-n1,
    .mx-xl-n1 {
      margin-left: -0.25rem !important;
    }
  
    .m-xl-n2 {
      margin: -0.5rem !important;
    }
  
    .mt-xl-n2,
    .my-xl-n2 {
      margin-top: -0.5rem !important;
    }
  
    .mr-xl-n2,
    .mx-xl-n2 {
      margin-right: -0.5rem !important;
    }
  
    .mb-xl-n2,
    .my-xl-n2 {
      margin-bottom: -0.5rem !important;
    }
  
    .ml-xl-n2,
    .mx-xl-n2 {
      margin-left: -0.5rem !important;
    }
  
    .m-xl-n3 {
      margin: -1rem !important;
    }
  
    .mt-xl-n3,
    .my-xl-n3 {
      margin-top: -1rem !important;
    }
  
    .mr-xl-n3,
    .mx-xl-n3 {
      margin-right: -1rem !important;
    }
  
    .mb-xl-n3,
    .my-xl-n3 {
      margin-bottom: -1rem !important;
    }
  
    .ml-xl-n3,
    .mx-xl-n3 {
      margin-left: -1rem !important;
    }
  
    .m-xl-n4 {
      margin: -1.5rem !important;
    }
  
    .mt-xl-n4,
    .my-xl-n4 {
      margin-top: -1.5rem !important;
    }
  
    .mr-xl-n4,
    .mx-xl-n4 {
      margin-right: -1.5rem !important;
    }
  
    .mb-xl-n4,
    .my-xl-n4 {
      margin-bottom: -1.5rem !important;
    }
  
    .ml-xl-n4,
    .mx-xl-n4 {
      margin-left: -1.5rem !important;
    }
  
    .m-xl-n5 {
      margin: -3rem !important;
    }
  
    .mt-xl-n5,
    .my-xl-n5 {
      margin-top: -3rem !important;
    }
  
    .mr-xl-n5,
    .mx-xl-n5 {
      margin-right: -3rem !important;
    }
  
    .mb-xl-n5,
    .my-xl-n5 {
      margin-bottom: -3rem !important;
    }
  
    .ml-xl-n5,
    .mx-xl-n5 {
      margin-left: -3rem !important;
    }
  
    .m-xl-auto {
      margin: auto !important;
    }
  
    .mt-xl-auto,
    .my-xl-auto {
      margin-top: auto !important;
    }
  
    .mr-xl-auto,
    .mx-xl-auto {
      margin-right: auto !important;
    }
  
    .mb-xl-auto,
    .my-xl-auto {
      margin-bottom: auto !important;
    }
  
    .ml-xl-auto,
    .mx-xl-auto {
      margin-left: auto !important;
    }
  }
  

  .form-label { 
    font-weight: bold;
    font-size: 10px;
    margin-bottom: 1px;
    overflow-wrap: break-word;
  }

  
.form-content { 
  min-height : 20px;
    font-size: 10px;
    overflow-wrap: break-word;
  }
  
  .divider {
    display: block;
    flex: 1 1 0px;
    max-width: 100%;
    height: 0px;
    max-height: 0px;
    border: solid;
    border-width: thin 0 0 0;
    transition: inherit;
    border-color: rgba(0, 0, 0, 0.12);
}
  `;


export default printCss;