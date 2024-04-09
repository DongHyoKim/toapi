const fs = require('fs');
const express = require('express');
const app = require('express')();
const cors = require('cors');
const axios = require('axios');
const qs = require('qs');
const port = 3000;

const httpsOptions = {
	/*
    key: fs.readFileSync('/etc/httpd/ssl/toapi3/toapi.cway.co.kr_20220203D274A.key.pem'),
    cert: fs.readFileSync('/etc/httpd/ssl/toapi3/toapi.cway.co.kr_20220203D274A.crt.pem'),
    requestCert: false,
    rejectUnauthorized: false
	*/
};
//const http = require('https').createServer(httpsOptions, app);
const http = require('http').createServer(httpsOptions, app);

app.use(express.json()); // for parsing application/json
app.use(
  express.urlencoded({
    extended: true,
  })
); // for parsing application/x-www-form-urlencoded
app.use(cors());

app.get('/', (req, res) => {
  res.send('Hello Cway ToApi');
});

app.post('/check_card', (req, res) => {
  console.log(`\n---------check_card start--------------` + printNow());

  axios
    .post(
      'https://toapi.cway.co.kr/commonapi/check_card',
      qs.stringify({
        univcode: req.body.univcode,
        sub_univcode: req.body.sub_univcode,
        card_no: req.body.card_no,
      }),
      {
        headers: {
          univcode: req.body.univcode,
        },
      }
    )
    .then(function (response) {
      //console.log(response);

      let data = {
        value: response.data.value,
        card_no: response.data.card_no,
        message: response.data.message,
        balance_amt: response.data.balance_amt,
      };
      console.log(data);
      res.json(data);
    })
    .catch(function (error) {
      console.log(error);
      /*
	  let data = {
        value: response.data.value,
        card_no: response.data.card_no,
        message: response.data.message,
        balance_amt: response.data.balance_amt,
      };
	  */
      //console.log(data);
      res.json(error);
    });

  console.log(`---------check_card end--------------\n` + printNow());
});

app.listen(port, () => console.log(`Cway ToApi listening on port ${port}`));

function printNow() {
  const today = new Date();

  const dayNames = [
    '(일요일)',
    '(월요일)',
    '(화요일)',
    '(수요일)',
    '(목요일)',
    '(금요일)',
    '(토요일)',
  ];
  // getDay: 해당 요일(0 ~ 6)를 나타내는 정수를 반환한다.
  const day = dayNames[today.getDay()];

  const year = today.getFullYear();
  let month = today.getMonth() + 1;
  let date = today.getDate();
  let hour = today.getHours();
  let minute = today.getMinutes();
  let second = today.getSeconds();
  const ampm = hour >= 12 ? 'PM' : 'AM';

  // 12시간제로 변경
  //hour %= 12;
  //hour = hour || 12; // 0 => 12

  month = month < 10 ? '0' + month : month;
  date = date < 10 ? '0' + date : date;

  // 10미만인 분과 초를 2자리로 변경
  minute = minute < 10 ? '0' + minute : minute;
  second = second < 10 ? '0' + second : second;

  const now = `${year}-${month}-${date} ${hour}:${minute}:${second}`;

  return now;
}
