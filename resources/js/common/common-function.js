import { isInteger } from "lodash";
import lodash from 'lodash';

var moment = require('moment');

export function isNumberAndSpace(value) {

    //to array
    if (value != null) {
        value = value.split('');
        if (
            value.filter(function (item, index) {

                return !parseInt(item) && parseInt(item) != 0 && item != ' ';
            }).length > 0
        ) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}
export function getArrayValues(data) {

    if (_.isArray(data) && !_.isEmpty(data)) {
        let str = '';
        _.forEach(data, function (item, index) {
            if (!_.isObject(item)) {
                if (index + 1 == data.length) {
                    str += `${item}`;
                } else {
                    str += `${item}, `;
                }
            }
        })
        return str;
    } else {
        return 'N/A';
    }
}

export function formatDate(value, format) {

    if (value != null) {
        if (!isValidDate(value)) {
            return null;
        } else {

            if (format == null) {
                format = "DD/MM/YYYY"
            }

            return moment(value).format(format);
        }
    } else {
        return null;
    }

}

export function formatNumber(value, unit, round, fixedPoint, trim) {

    if (value != null) {
        if (isNaN(parseFloat(value))) {
            return value;
        } else {

            value = parseFloat(value);
            //default will auto round up if round didn't passed in
            //default fixedPoint 0
            switch (unit) {
                case "k":
                    value = numberToFixed(value / 1000, round, fixedPoint);
                    break;
                case "m":
                    value = numberToFixed(value / 1000000, round, fixedPoint);
                    break;
                case "b":
                    value = numberToFixed(value / 1000000000, round, fixedPoint);
                    break;
                case "auto":
                    let units = [['', 1], ['k', 1000], ['m', 1000000], ['b', 1000000000]];
                    //get closest unit
                    _.forEach(_.reverse(units), function (item, index) {
                        if (value / item[1] >= 1) {
                            value = numberToFixed(value / item[1], round, fixedPoint);
                            unit = item[0];
                            return false;
                        }
                    })
                    break;
                default:
                    value = numberToFixed(value, round, fixedPoint);
                    break;
            }

            let formatedValue = '';
            let prefix = insertBetween(parseInt(value), 3, ',', true, true);
            let postfix = value.toString().split('.')[1];
            formatedValue += prefix;
            if (postfix) {
                formatedValue += '.' + postfix;
            }

            if (trim) {
                formatedValue = trimStringNumber(formatedValue);
            }
            if (unit) {
                formatedValue += unit;
            }



            return formatedValue;
        }
    } else {
        return value;
    }

}

export function numberToFixed(value, round, fixedPoint, fromFront) {

    if (value != null && !isNaN(parseFloat(value))) {

        if (isNaN(parseInt(fixedPoint))) {
            //Default
            fixedPoint = 0;
        } else {
            fixedPoint = parseInt(fixedPoint);
        }

        if (round == null) {
            round = true;
        }

        if (fromFront) {
            value = parseFloat(value);
            value = value.toString().split('.');
            if (value[0]) {
                // if (value[0].length > fixedPoint) {
                //     value[0] = value[0].slice(value[0].length - fixedPoint);
                // }

                if (value[0].length < fixedPoint) {
                    _.forEach(_.range(fixedPoint - value[0].length), function () {
                        value[0] = '0' + value[0];
                    })
                }

                return value[0];
            }
        } else {
            value = parseFloat(value);
            if (round) {
                return value.toFixed(fixedPoint);
            } else {
                value = value.toString().split('.');
                if (value[1]) {
                    if (value[1].length > fixedPoint) {
                        value[1] = value[1].slice(0, fixedPoint);
                    }

                    if (value[1].length < fixedPoint) {
                        _.forEach(_.range(fixedPoint - value[0].length), function () {
                            value[0] = value[0] + '0';
                        })
                    }
                }

                return parseFloat(value.join(".")).toFixed(fixedPoint);
            }
        }


    } else {
        return value;
    }

}

//Remove prefix 0 and postfix 0
export function trimStringNumber(value) {

    if (value != null) {

        value = value.toString().split('.');
        let prefix = value[0];
        let postfix = value[1];

        if (prefix != null) {
            if (prefix.length > 1) {
                let prefixArr = prefix.split("");
                let done = false;
                prefixArr.some(num => {
                    if (num == '0') {
                        //cut out 0
                        prefix = prefix.substring(1);
                    } else {
                        done = true;
                    }
                    return done;
                });
            }
        }

        if (postfix != null) {
            if (postfix.length > 0) {
                let postfixArr = postfix.split("").reverse();
                let done = false;
                postfixArr.some(num => {
                    if (num == '0') {
                        //cut out 0
                        postfix = postfix.substring(0, postfix.length - 1);
                    } else {
                        done = true;
                    }
                    return done;
                });
            }
        }
        if (postfix) {
            return prefix + "." + postfix;
        } else {
            return prefix;
        }

    } else {
        return value;
    }

}

export function reverseString(value) {

    if (value != null) {
        var strArray = value.toString().split("");
        strArray = strArray.reverse();
        return strArray.join("");
    } else {
        return value;
    }

}


export function checkCardType(number) {
    if (number != null) {

        number = number.replace(/\s/g, '');
        // visa
        var re = new RegExp("^4");
        if (number.match(re) != null)
            return "VISA";

        // Mastercard 
        // Updated for Mastercard 2017 BINs expansion
        if (/^(5[1-5][0-9]{14}|2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12}))$/.test(number))
            return "MASTERCARD";

        // AMEX
        re = new RegExp("^3[47]");
        if (number.match(re) != null)
            return "AMERICANEXPRESS";

        // Discover
        re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
        if (number.match(re) != null)
            return "DISCOVER";

        // Diners
        re = new RegExp("^36");
        if (number.match(re) != null)
            return "DINERS";

        // Diners - Carte Blanche
        re = new RegExp("^30[0-5]");
        if (number.match(re) != null)
            return "DINERSCARTEBLANCHE";

        // JCB
        re = new RegExp("^35(2[89]|[3-8][0-9])");
        if (number.match(re) != null)
            return "JCB";

        // Visa Electron
        re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
        if (number.match(re) != null)
            return "VISAELECTRON";

    }
    return null;
}

export function removeNullFromArray(value) {

    if (notEmptyLength(value)) {
        return value.filter(function (item) {
            return item != null;
        })
    }

    return value;
}
export function isValidDate(value) {

    if (value != null) {
        value = new Date(value);
        if (Object.prototype.toString.call(value) === "[object Date]") {
            // it is a date
            if (isNaN(value.valueOf())) {  // value.valueOf() could also work
                return false;
            } else {
                return true
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

export function calculateTimeRange(date1, date2, unit, precise) {

    if (date1 != null) {
        if (isValidDate(date1)) {
            date1 = moment(date1);

            if (date2 != null) {
                if (isValidDate(date2)) {
                    date2 = moment(date2);
                } else {
                    return null;
                }
            } else {
                date2 = moment();
            }

            let data = {};
            switch (unit) {
                case 'year':
                    data.difference = date1.diff(date2, 'years', precise);
                    data.unit = unit;
                    break;
                case 'month':
                    data.difference = date1.diff(date2, 'months', precise);
                    data.unit = unit;
                    break;
                case 'week':
                    data.difference = date1.diff(date2, 'weeks', precise);
                    data.unit = unit;
                    break;
                case 'day':
                    data.difference = date1.diff(date2, 'days', precise);
                    data.unit = unit;
                    break;
                case 'hour':
                    data.difference = date1.diff(date2, 'hours', precise);
                    data.unit = unit;
                    break;
                case 'minute':
                    data.difference = date1.diff(date2, 'minutes', precise);
                    data.unit = unit;
                    break;
                case 'second':
                    data.difference = date1.diff(date2, 'seconds', precise);
                    data.unit = unit;
                    break;

                default:
                    //auto get nearest 1
                    let sections = ['year', 'month', 'week', 'day', 'hour', 'minute', 'second']
                    let selectedSection = sections.find(function (section) {
                        return Math.abs(date1.diff(date2, section + 's', true)) >= 1;
                    })
                    if (selectedSection) {
                        data.difference = date1.diff(date2, selectedSection + 's', precise);
                        data.unit = selectedSection;
                    } else {
                        data.difference = 0;
                        data.unit = 'second';
                    }
            }

            return data;
        } else {
            return null;
        }
    } else {
        return null;
    }

}

export function convertMilliSecondsToTime(millisecond, minUnit) {

    if (millisecond != null && isValidNumber(parseInt(millisecond))) {

        let second = 0, minute = 0, hour = 0;
        let finalTime = '';
        millisecond = parseInt(millisecond);

        hour = numberToFixed(moment.duration(millisecond).hours(), false, 2, true);
        minute = numberToFixed(moment.duration(millisecond).minutes(), false, 2, true);
        second = numberToFixed(moment.duration(millisecond).seconds(), false, 2, true);

        switch (minUnit) {
            case 'minute':
                if (hour > 0) {
                    finalTime = `${hour}:${minute}:${second}`;
                } else {
                    finalTime = `${minute}:${second}`;
                }
                break;
            case 'hour':
                finalTime = `${hour}:${minute}:${second}`;
                break;

            default:
                if (hour > 0) {
                    finalTime = `${hour}:${minute}:${second}`;
                } else if (minute > 0) {
                    finalTime = `${minute}:${second}`;
                } else {
                    finalTime = `${second}`;
                }
                break;
        }

        return finalTime;
    } else {
        return null;
    }

}
export function isExpired(date, aspect) {

    if (aspect == null) {
        aspect = 'second';
    }

    if (date != null && isValidDate(date)) {
        var today = moment();
        date = moment(date);
        return today.isAfter(date, aspect);
    }
    return null;
}
export function convertToCardFormat(value) {

    if (value != null) {
        value = value.replace(/\s/g, '');
        value = insertBetween(value, 4, ' ');
    }

    return value
}

export function convertToExpiredDateFormat(value) {

    if (value != null) {
        var str = value.split('');
        if (str.length < 3) {
            if (str[1] == '/') {
                str.splice(0, 0, '0');
                value = str.join("");
            } else {
                value = value.replace('/', '');
                value = insertBetween(value, 2, '/');
            }
        }
    }

    return value;

}

export function insertBetween(value, space, char, fromBack, stopAtEnd) {

    if (value != null) {
        if (isNaN(parseInt(space))) {
            return value;
        } else {
            if (fromBack) {
                value = reverseString(value);
            }
            var oristr = value.toString().split('');
            var length = oristr.length;
            var addedspacecount = 1;
            space = parseInt(space);
            for (let index = 0; index < length; index++) {
                if (index != 0 && (index + 1) % space == 0) {
                    if (stopAtEnd && index == length - 1) {
                        break;
                    }
                    oristr.splice(index + addedspacecount, 0, char);
                    addedspacecount = addedspacecount + 1;
                }
            }
            if (fromBack) {
                oristr = oristr.reverse();
            }
            return oristr.join("");
        }
    } else {
        return null;
    }

}

export function checkSupportedCardType(card) {
    if (card != null) {
        // visa
        if (card == 'VISA') {
            return true;
        }

        if (card == 'MASTERCARD') {
            return true;
        }

        if (card == 'VISAELECTRON') {
            return true;
        }

        if (card == 'AMERICANEXPRESS') {
            return true;
        }

    }
    return false;
}
export function isValidNumber(value) {
    return !isNaN(parseFloat(value));
}
export function isExpiryDateFormat(value) {

    if (value != null) {
        //to array
        value = value.split('');
        if (
            value.filter(function (item, index) {
                if (index == 2) {
                    return item != '/';
                }

                return !parseInt(item) && parseInt(item) != 0;
            }).length > 0
        ) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

export function sortByDesc(data, col) {

    if (data && col) {
        return data.sort(function (a, b) {
            if (a[col] < b[col]) return 1;
            if (a[col] > b[col]) return -1;
            return 0;
        });
    } else {
        return [];
    }
}

export function sortByDateDesc(data, col) {

    if (data && col) {
        return data.sort(function (a, b) {
            if (moment(a[col]).isBefore(moment(b[col]))) return 1;
            if (moment(a[col]).isAfter(moment(b[col]))) return -1;
            return 0;
        });
    } else {
        return [];
    }
}

export function findData(data, col, val) {
    return data.filter(function (item) {
        return item[col] == val;
    })
}
export function hideStringNumber(string, start, end) {
    if (string) {
        var length = string.length;

        //Get rest part
        var startpart = '';
        var endpart = '';
        if (Number.isInteger(start) && Number.isInteger(end)) {
            startpart = string.slice(0, start);
            endpart = string.slice(length - (length - end), length);
        } else if (Number.isInteger(start)) {
            startpart = string.slice(0, start);
            endpart = '';
        } else if (Number.isInteger(end)) {
            startpart = '';
            endpart = string.slice(length - (length - end), length);
        } else {
            return string;
        }

        //Get desire convert part
        var convertpart = '';
        if (Number.isInteger(start) && Number.isInteger(end)) {
            convertpart = string.slice(start, end);
        } else if (Number.isInteger(start)) {
            convertpart = string.slice(start)
        } else if (Number.isInteger(end)) {
            convertpart = string.slice(0, end)
        } else {
            return string;
        }

        //to array
        convertpart = convertpart.split('');
        convertpart = convertpart.map(function (char) {
            if (isNaN(parseInt(char)) || !parseInt(char)) {
                return char;
            } else {
                return '*';
            }
        })

        //to string
        convertpart = convertpart.join("");

        return startpart + convertpart + endpart;



    } else {
        return null;
    }

}
export function notEmptyLength(data) {
    if (data) {
        if (Array.isArray(data) && data.length > 0) {
            return true;
        } else if (isObject(data) && Object.keys(data).length > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
export function arrayLengthCount(data) {

    if (notEmptyLength(data) && Array.isArray(data)) {
        return data.length;
    } else {
        return 0;
    }
}


export function syncDefaultDelivery(data, id) {
    return data.map(function (item) {
        if (item._id) {
            if (item._id != id) {
                if (item.isDefaultDelivery) {
                    item.isDefaultDelivery = false;
                }
            } else {
                item.isDefaultDelivery = true;
            }
        }

        return item;
    })
}

export function syncDefaultBilling(data, id) {
    return data.map(function (item) {
        if (item._id) {
            if (item._id != id) {
                if (item.isDefaultBilling) {
                    item.isDefaultBilling = false;
                }
            } else {
                item.isDefaultBilling = true;
            }
        }

        return item;
    })
}


export function syncDefaultCard(data, id) {
    return data.map(function (item) {
        if (item._id) {
            if (item._id != id) {
                if (item.isDefaultCard) {
                    item.isDefaultCard = false;
                }
            } else {
                item.isDefaultCard = true;
            }
        }

        return item;
    })
}
export function syncDefaultBank(data, id) {
    return data.map(function (item) {
        if (item._id) {
            if (item._id != id) {
                if (item.isDefaultBank) {
                    item.isDefaultBank = false;
                }
            } else {
                item.isDefaultBank = true;
            }
        }

        return item;
    })
}

export function isSavedWishList(data, id) {
    if (data && id) {
        var check = data.filter(function (item) {
            return item.productId == id;
        });

        return check.length > 0;
    } else {
        return false;
    }

}

export function isFollowed(data, id) {
    if (data && id) {
        var check = data.filter(function (item) {
            return item.companyId == id;
        });

        return check.length > 0;
    } else {
        return false;
    }

}

export function deepEqual(object1, object2) {
    if (object1 && object2) {
        const keys1 = Object.keys(object1);
        const keys2 = Object.keys(object2);

        if (keys1.length !== keys2.length) {
            return false;
        }

        for (const key of keys1) {
            const val1 = object1[key];
            const val2 = object2[key];
            const areObjects = isObject(val1) && isObject(val2);
            if (
                areObjects && !deepEqual(val1, val2) ||
                !areObjects && val1 !== val2
            ) {
                return false;
            }
        }

    } else {
        return false
    }

    return true;
}

export function isObject(object) {
    return object != null && typeof object === 'object';
}

export function deepEqualArrayObject(array1, array2) {


    if (Array.isArray(array1) && Array.isArray(array2)) {
        if (array1.length != array2.length) {
            return false;
        } else {
            for (let x = 0; x < array1.length; x++) {
                if (!deepEqual(array1[x], array2[x])) {
                    return false;
                }
            }
            return true;
        }


    } else {
        return false;
    }
}


export function roundToHalf(number) {

    if (number != null) {
        if (!isNaN(parseFloat(number))) {
            var decimal = parseFloat(number);
            var int = parseInt(number);

            decimal = decimal - int;
            return decimal >= 0.5 ? int + 0.5 : int;

        }
    }

    return false;
}


export function queryStringifyNestedObject(value) {

    if (value != null) {
        return queryString.stringify({
            data: JSON.stringify(value)
        })
    }

    return '';
}

export function getImgTagSource(str) {
    if (str) {
        str = str.toString();
        let strArr = str.split("");
        let removed = true;
        //Get html tag only
        strArr = _.compact(_.map(strArr, function (chr) {
            if (chr == '<') {
                removed = false;
                return chr;
            }

            if (chr == '>') {
                removed = true;
                return chr;
            }
            if (removed) {
                return null;
            }
            return chr;
        }));

        let images = strArr.join("").split(">");
        images = _.compact(_.map(images, function (image) {
            if (image.substr(0, 4) == '<img') {
                //point to src value
                let startIndex = image.indexOf('src="') + 5;

                //crop until src
                let croppedStrArr = image.substr(startIndex)
                croppedStrArr = croppedStrArr.split("");
                let removed = false;

                //retrieve value
                croppedStrArr = _.compact(_.map(croppedStrArr, function (chr) {
                    if (chr == '"') {
                        removed = true;
                        return null;
                    }
                    if (removed) {
                        return null;
                    }
                    return chr;
                }));

                return { url: `/${croppedStrArr.join("")}`, name: croppedStrArr.join("").slice(10) };

            }

            return null;
        }))
        return images;
    } else {
        return str;
    }
}

export function removeHtmlTag(str) {
    if (str) {
        str = str.toString();
        let strArr = str.split("");
        let removed = false;
        strArr = _.compact(_.map(strArr, function (chr) {
            if (chr == '<') {
                removed = true;
                return null;
            }

            if (chr == '>') {
                removed = false;
                return null;
            }
            if (removed) {
                return null;
            }
            return chr;
        }));

        return strArr.join("");
    } else {
        return str;
    }
}

export function convertFilterRange(value, name) {

    if (notEmptyLength(value) && name) {
        let parameter1 = parseFloat(value[0]);
        let parameter2 = value[1];
        let finalData = [];
        let query = {};

        if (!isValidNumber(parameter1)) {
            return null;
        }

        if (parameter2 != null) {

            switch (parameter2) {
                case '<=':
                    query[`${name}`] = { $lte: +(parameter1) }
                    finalData.push(query);
                    break;
                case '<':
                    query[`${name}`] = { $lt: +(parameter1) }
                    finalData.push(query);
                    break;
                case '==':
                    query[`${name}`] = { $eq: +(parameter1) }
                    finalData.push(query);
                    break;
                case '>':
                    query[`${name}`] = { $gt: +(parameter1) }
                    finalData.push(query);
                    break;
                case '>=':
                    query[`${name}`] = { $gte: +(parameter1) }
                    finalData.push(query);
                    break;
                default:
                    parameter2 = parseFloat(parameter2);
                    if (!isValidNumber(parameter2)) {
                        query[`${name}`] = { $eq: +(parameter1) }
                        finalData.push(query);
                    } else {
                        query[`${name}`] = { $gte: +(parameter1) }
                        finalData.push(query);

                        let query1 = {};
                        query1[`${name}`] = { $lte: +(parameter2) }
                        finalData.push(query1);
                    }
                    break;
            }

        } else {
            query[`${name}`] = { $eq: +(parameter1) }
            finalData.push(query);
        }
        return finalData;
    } else {
        return null;
    }
}

export function objectRemoveEmptyValue(value) {

    if (notEmptyLength(value) && _.isObject(value)) {
        return _.pickBy(value, function (property) {
            return _.isArray(property) ? notEmptyLength(_.compact(property)) : _.isObject(property) ? notEmptyLength(objectRemoveEmptyValue(property)) : property;
        });
    } else {
        return {}
    }
}

export function convertParameterToProductListUrl(data, config) {

    let mergeObj = objectRemoveEmptyValue(data);
    let basePath = '';

    if (!notEmptyLength(config)) {
        config = {
            page: 1,
            view: 'gridView',
            sorting: 'createdAt:-1',
        }
    }

    if (!isValidNumber(parseInt(config.page))) {
        config.page = 1;
    }

    if (!config.view) {
        config.view = 'gridView';
    }

    if (!config.sorting) {
        config.sorting = 'createdAt:-1';
    }

    if (notEmptyLength(mergeObj)) {
        let condition = mergeObj.condition;
        let make = mergeObj.make;
        let model = mergeObj.model;
        let state = mergeObj.state;


        if (condition && condition != 'cars-on-sale') {
            condition = [_.toLower(condition), 'cars-on-sale'].join('-');
        } else {
            condition = 'cars-on-sale';
        }

        if (state && state != 'malaysia') {
            state = ['malaysia', _.toLower(mergeObj.state)].join('_');
        } else {
            state = 'malaysia';
        }

        //Main parameter
        //Order is important
        //The first 1 always is condition
        //The last 1 always is state
        let mainParameters = [make, model];
        let path = `${basePath}/${condition}`;

        _.forEach(mainParameters, function (parameter) {
            if (!parameter) {
                return false;
            } else {
                path += `/${_.toLower(parameter)}`
            }
        })

        path += `/${state}`
        delete mergeObj.condition;
        delete mergeObj.make;
        delete mergeObj.model;
        delete mergeObj.state;
        return `${path}?page=${config.page}&view=${config.view}${config.sorting ? `&sorting=${config.sorting}` : ''}${notEmptyLength(mergeObj) ? `&${queryStringifyNestedObject(mergeObj)}` : ''}`;

    } else {
        return `/cars-on-sale/malaysia?page=${config.page}&view=${config.view}${config.sorting ? `&sorting=${config.sorting}` : ''}`;
    }
}

export const _ = lodash;

export const accessRule = {
    all: {
        all: true,
        nav: true,
    },
    noAccess: {
        all: false,
        nav: false,
    },
    onlyEdit: {
        nav: true,
        tableView: true,
        view: true,
        create: false,
        edit: true,
        delete: false,
    },
    modify: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: true,
        delete: false,
    },
    onlyCreate: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: false,
        delete: false,
    },
    onlyTableView: {
        all: false,
        nav: true,
        tableView: true,
        view: false,
        create: false,
        edit: false,
        delete: false,
    },
    onlyView: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: false,
        edit: false,
        delete: false,
    },
    control : {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: true,
        delete: true,
    },
    onlyMakePayment: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: false,
        edit: false,
        delete: false,
        makePayment: true,
        print: true,
    },
    onlyPrint: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: false,
        edit: false,
        delete: false,
        makePayment: false,
        print: true,
    },
    onlyCreatePayment: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: false,
        delete: false,
        makePayment: false,
        print: false,
    },
    onlyEditPayment: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: false,
        edit: true,
        delete: false,
        makePayment: false,
        print: false,
    },
    editPaymentWithPrint: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: false,
        edit: true,
        delete: false,
        makePayment: true,
        print: true,
    },
    createPaymentWithPrint: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: false,
        delete: false,
        makePayment: false,
        print: true,
    },
    modifyPayment: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: true,
        delete: false,
        makePayment: false,
        print: false,
    },
    controlPayment: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: true,
        delete: true,
        makePayment: false,
        print: false,
    },
    accessPayment: {
        all: false,
        nav: true,
        tableView: true,
        view: true,
        create: true,
        edit: true,
        delete: false,
        makePayment: true,
        print: true,
    },
}

export const roleAccess = {
    superadmin: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `all`) || {})
            },
            staff: {
                ...(_.get(accessRule, `all`) || {})
            },
            owner: {
                ...(_.get(accessRule, `all`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `all`) || {})
            },
            service: {
                ...(_.get(accessRule, `all`) || {})
            },
            room: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `all`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `all`) || {})
            },
        }
    },
    admin: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `all`) || {})
            },
            staff: {
                ...(_.get(accessRule, `all`) || {})
            },
            owner: {
                ...(_.get(accessRule, `all`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `all`) || {})
            },
            service: {
                ...(_.get(accessRule, `all`) || {})
            },
            room: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `all`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `all`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `all`) || {})
            },
        }
    },
    maintenance_department: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            staff: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            owner: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            service: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            room: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `onlyCreate`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
        }
    },
    sale_and_marketing: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            staff: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            owner: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `onlyCreate`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            service: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            room: {
                ...(_.get(accessRule, `onlyTableView`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
        },
    },
    customer_service: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `all`) || {})
            },
            staff: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            owner: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `onlyCreate`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            service: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            room: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `onlyCreate`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `onlyMakePayment`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
        }
    },
    account_clerk: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `all`) || {})
            },
            staff: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            owner: {
                ...(_.get(accessRule, `modify`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `onlyEdit`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            service: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            room: {
                ...(_.get(accessRule, `modify`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `onlyEdit`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `editPaymentWithPrint`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `accessPayment`) || {})
            },
        }
    },
    asset_management: {
        modules: {
            dashboard: {
                ...(_.get(accessRule, `all`) || {})
            },
            staff: {
                ...(_.get(accessRule, `noAccess`) || {})
            },
            owner: {
                ...(_.get(accessRule, `modify`) || {})
            },
            tenant: {
                ...(_.get(accessRule, `modify`) || {})
            },
            roomType: {
                ...(_.get(accessRule, `modify`) || {})
            },
            service: {
                ...(_.get(accessRule, `modify`) || {})
            },
            room: {
                ...(_.get(accessRule, `modify`) || {})
            },
            roomCheck: {
                ...(_.get(accessRule, `onlyCreate`) || {})
            },
            roomContract: {
                ...(_.get(accessRule, `modify`) || {})
            },
            rentalPayment: {
                ...(_.get(accessRule, `createPaymentWithPrint`) || {})
            },
            roomMaintenance: {
                ...(_.get(accessRule, `onlyView`) || {})
            },
        }
    },
}

export function checkIsAccessible(role, module, action) {

    if (!role || !module) {
        return false;
    }

    return _.get(roleAccess, `${role}.modules.${module}.${action}`) || _.get(roleAccess, `${role}.modules.${module}.all`)

}

export function getDaysInMonth(month, year = 2012) {
    // Here January is 1 based
    //Day 0 is the last day in the previous month
    if(month > 0 && month < 13){
        return new Date(year, month, 0).getDate();
    }

    return 0;
    // Here January is 0 based
    // return new Date(year, month+1, 0).getDate();
};

export function calculateAge(birthday) { // birthday is a date
    var ageDifMs = Date.now() - birthday.getTime();
    var ageDate = new Date(ageDifMs); // miliseconds from epoch
    return Math.abs(ageDate.getUTCFullYear() - 1970);
}

