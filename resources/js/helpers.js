
import moment from "moment"; //Import Moment

const helpers = {
    toDouble(data) {
        var double = parseFloat(data).toFixed(2);
        if (double == "NaN") {
            double = 0;
        }
        return double;
    },
    capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },
    isPhoneFormat(value) {
        if (value) {
            if (value.length >= 0) {
                var splitvalue = value.split("-");
                //All special char except "-"
                var format = /[`~!@#$%^&*()_+\=\[\]{};':"\\|,.<>\/?]+/;

                if (value.charAt(3) == "-" && splitvalue.length == 2 && !value.match(/[a-z]/i) && !format.test(value)) {
                    return true;
                }
            }
        }

        return false;
    },
    isEmpty(value) {
        if (value == null || value == 'undefined' || value == "" || value == "N/A" || value == [] || value == {}) {
            return true;
        }

        return false;
    },
    isIcFormat(value) {
        if (value) {
            if (value.length >= 0 && value.length == 14) {
                var splitvalue = value.split("-");
                //All special char except "-"
                var format = /[`~!@#$%^&*()_+\=\[\]{};':"\\|,.<>\/?]+/;

                if (value.charAt(6) == "-" && value.charAt(9) == "-" && splitvalue.length == 3 && !value.match(/[a-z]/i) && !format.test(value)) {
                    return true;
                }
            }
        }

        return false;
    },
    notGmail(value = "") {
        return !value.includes("gmail");
    },
    isEmailAvailable(value) {
        if (value === "") return true;

        return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(value.length > 10);
            }, 500);
        });


    },

    sortBy(data, col) {
        if(this.isEmpty(data) || this.isEmpty(col)){
            return null;
        }

        data = data.sort(function(a, b){
            return a[col] - b[col];
        })

    },
    

    sortByDate(data, col) {
        if(this.isEmpty(data) || this.isEmpty(col)){
            return [];
        }

        data = data.sort(function(a, b){
            if(moment(a[col]).isSame(moment(b[col]), 'year') && moment(a[col]).isSame(moment(b[col]), 'month')){
                console.log('same');
                return 0;
            }else if(moment(a[col]).isSameOrBefore(moment(b[col]), 'year') && moment(a[col]).isBefore(moment(b[col]), 'month')){
                console.log('small');
                return -1;
            }else{
                console.log('big');
                return 1;
            }
        })

        return data;

    },

    includedInArray(value, array, col) {
        if(this.isEmpty(value) || this.isEmpty(array) || this.isEmpty(col)){
            return [];
        }

       return array.includes(function(item){
            return item[col] == value;
        })


    },
    // filterWithArrayCol(array1, array2, array1Col , array2Col) {
    //     if(this.isEmpty(array1) || this.isEmpty(array2) || this.isEmpty(array1Col)|| this.isEmpty(array2Col)){
    //         return [];
    //     }

    //     var data = array1.filter(function(array1Item){
    //         return array2.find(function(array2item){
    //             return array2item[array2Col] == array1Item[array1Col];
    //         })
    //     })

    //     return data;

    // },
    //This data centralization is only valid for static constant data.
    //If you are consider for using dynamic variable data can be modified by web. Please use vuex instead =)
    managementStyles() {
        const styles = {
            backgroundClass: 'grey lighten-2',
            backgroundColor: '#EEEEEE',
            formCardClass: 'grey lighten-5',
            formCardColor: '#FAFAFA',
            dividerColor: '#ffffff',
            mainButtonColor: 'blue lighten-3',
            titleClass : 'font-weight-black display-1 d-inline-block',
            subtitleClass : 'font-weight-bold title d-inline-block',
            lightSubtitleClass : 'font-weight-regular title d-inline-block ',
            textClass : 'font-weight-regular title-1 d-inline-block',
            centerWrapperClass : 'd-flex flex-wrap justify-center align-center',

        }

        return styles;

    },

};


export { helpers };