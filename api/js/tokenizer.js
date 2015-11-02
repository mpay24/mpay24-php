function checkValid(d) {
  var data = JSON.parse(d.data);
  if (data.valid === "true") {
    pay.disabled = false;
  }
}
