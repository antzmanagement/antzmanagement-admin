
import { helpers } from "./helpers";
//Vue moment js to show human readable date
import moment from "moment"; //Import Moment

//Vue Filter to make first letter Capital
Vue.filter("capitalizeFirstLetter", function (text) {

	return helpers.capitalizeFirstLetter(text);

});


Vue.filter("formatDate", function (date) {

	if (date) {
		return moment(date).format('MMMM Do YYYY');
	}else{
		return 'N/A'
	}

});


Vue.filter("toDouble", function (value) {

	return helpers.toDouble(value);

}); 