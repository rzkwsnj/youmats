var $cc = {}
$cc.validate = function(e){

  //if the input is empty reset the indicators to their default classes
  if (e.target.value == ''){
    e.target.previousElementSibling.className = 'card-type';
    e.target.nextElementSibling.className = 'card-valid';
    return
  }

  //Retrieve the value of the input and remove all non-number characters
  var number = String(e.target.value);
  var cleanNumber = '';
  for (var i = 0; i<number.length; i++){
    if (/^[0-9]+$/.test(number.charAt(i))){
      cleanNumber += number.charAt(i);
    }
  }

  //Only parse and correct the input value if the key pressed isn't backspace.
  if (e.key != 'Backspace'){
    //Format the value to include spaces in the correct locations
    var formatNumber = '';
    for (var i = 0; i<cleanNumber.length; i++){
      if (i == 3 || i == 7 || i == 11 ){
          formatNumber = formatNumber + cleanNumber.charAt(i) + ' '
      }else{
        formatNumber += cleanNumber.charAt(i)
      }
    }
    e.target.value = formatNumber;
  }

  //run the Luhn algorithm on the number if it is at least equal to the shortest card length
  if (cleanNumber.length >= 12){
    var isLuhn = luhn(cleanNumber);
  }

  function luhn(number){
    var numberArray = number.split('').reverse();
    for (var i=0; i<numberArray.length; i++){
      if (i%2 != 0){
        numberArray[i] = numberArray[i] * 2;
        if (numberArray[i] > 9){
          numberArray[i] = parseInt(String(numberArray[i]).charAt(0)) + parseInt(String(numberArray[i]).charAt(1))
        }
      }
    }
    var sum = 0;
    for (var i=1; i<numberArray.length; i++){
      sum += parseInt(numberArray[i]);
    }
    sum = sum * 9 % 10;
    if (numberArray[0] == sum){
      return true
    }else{
      return false
    }
  }

  //if the number passes the Luhn algorithm add the class 'active'
  if (isLuhn == true){
    e.target.nextElementSibling.className = 'card-valid active'
  }else{
    e.target.nextElementSibling.className = 'card-valid'
  }

  var card_types = [
    {
      name: 'amex',
      pattern: /^3[47]/,
      valid_length: [15]
    },
    {
      name: 'mada',
      pattern: /^(58845|440647|440795|410621|420132|457997|474491|558563|446404|457865|968208|636120|417633|468540|468541|468542|468543|968201|446393|409201|458456|484783|968205|462220|455708|588848|455036|968203|486094|486095|486096|504300|440533|489318|489319|445564|968211|410685|406996|432328|428671|428672|428673|968206|446672|543357|434107|407197|407395|412565|431361|604906|521076|588850|968202|529415|535825|543085|524130|554180|549760|968209|524514|529741|537767|535989|536023|513213|520058|585265|588983|588982|589005|508160|531095|530906|532013|605141|968204|422817|422818|422819|428331|483010|483011|483012|589206|968207|419593|439954|530060|531196)/,
      valid_length: [16]
    },

    {
      name: 'visa',
      pattern: /^4/,
      valid_length: [16]
    }, {
      name: 'mastercard',
      pattern: /^5[1-5]/,
      valid_length: [16]
    }
  ];

  //test the number against each of the above card types and regular expressions, after 6 digits and only at default state
  if(number.length>6 && e.target.previousElementSibling.className=="card-type")
  for (var i = 0; i< card_types.length ; i++){
      let num=number.replace(/\s+/g, '');   // Removing any extra spaces
      
    if (num.match(card_types[i].pattern)){

      //if a match is found add the card type as a class
      e.target.previousElementSibling.className = 'card-type '+card_types[i].name;
      break;
        
    }

  }


}

$cc.expiry = function(e){
  if (e.key != 'Backspace'){
    var number = String(this.value);

    //remove all non-number character from the value
    var cleanNumber = '';
    for (var i = 0; i<number.length; i++){
      if (i == 1 && number.charAt(i) == '/'){
        cleanNumber = 0 + number.charAt(0);
      }
      if (/^[0-9]+$/.test(number.charAt(i))){
        cleanNumber += number.charAt(i);
      }
    }

    var formattedMonth = ''
    for (var i = 0; i<cleanNumber.length; i++){
      if (/^[0-9]+$/.test(cleanNumber.charAt(i))){
        //if the number is greater than 1 append a zero to force a 2 digit month
        if (i == 0 && cleanNumber.charAt(i) > 1){
          formattedMonth += 0;
          formattedMonth += cleanNumber.charAt(i);
          formattedMonth += '/';
        }
        //add a '/' after the second number
        else if (i == 1){
          formattedMonth += cleanNumber.charAt(i);
          formattedMonth += '/';
        }
        //force a 4 digit year
        else if (i == 2 && cleanNumber.charAt(i) <2){
          formattedMonth += '20' + cleanNumber.charAt(i);
        }else{
          formattedMonth += cleanNumber.charAt(i);
        }

      }
    }
    this.value = formattedMonth;
  }
}
