
import { helpers } from "./helpers";
//Vue moment js to show human readable date
import moment from "moment"; //Import Moment
import _ from 'lodash';

//Vue Filter to make first letter Capital
Vue.filter("capitalizeFirstLetter", function (text) {

	return helpers.capitalizeFirstLetter(text);

});


Vue.filter("formatDate", function (date) {

	if (date) {
		return moment(date).format('DD/MM/YYYY');
	} else {
		return 'N/A'
	}

});


Vue.filter("getArrayValues", function (data) {

	if (_.isArray(data) && !_.isEmpty(data)) {
		let str = '';
		_.forEach(data, function (item, index) {
			if (!_.isObject(item)) {
				if(index + 1 == data.length){
					str += `${item}`;
				}else{
					str += `${item}, `;
				}
			}
		})
		return str;
	} else {
		return 'N/A';
	}


});
Vue.filter("toDouble", function (value) {

	return helpers.toDouble(value).toFixed(2);

}); 