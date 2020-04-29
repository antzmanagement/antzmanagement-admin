const helpers = {
    toDouble(data){
        var double = parseFloat(data).toFixed(2);
        if(double == "NaN"){
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
        if (value == null || value == 'undefined' || value == ""  || value == "N/A" || value == []) {
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
    
};


export { helpers };