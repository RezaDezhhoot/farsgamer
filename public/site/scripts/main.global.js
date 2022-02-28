function ActiveDeActive(elem) {
  if (elem.classList.contains("active-li")) {
    elem.classList.remove("active-li");
  } else {
    elem.classList.add("active-li");
  }
}
const AccountInfo = document.getElementsByClassName("Account-info");
function showAccountInfo() {
  if (AccountInfo[0].classList.contains("d-none")) {
    AccountInfo[0].classList.add("d-flex");
    AccountInfo[0].classList.remove("d-none");
  } else {
    AccountInfo[0].classList.remove("d-flex");
    AccountInfo[0].classList.add("d-none");
  }
}
const SignUp = document.getElementsByClassName("SignUp");
const Login = document.getElementsByClassName("Login");
const Confirmation = document.getElementsByClassName("Confirmation");
function loginFun(num) {
  if (num === 1) {
    Login[0].classList.remove("d-flex");
    Login[0].classList.add("d-none");
    SignUp[0].classList.add("d-none");
    SignUp[0].classList.remove("d-flex");
    Confirmation[0].classList.remove("d-none");
    Confirmation[0].classList.add("d-flex");
  } else if (num === 0) {
    Login[0].classList.remove("d-none");
    Login[0].classList.add("d-flex");
    Confirmation[0].classList.remove("d-flex");
    Confirmation[0].classList.add("d-none");
  } else {
    Login[0].classList.add("d-flex");
    Login[0].classList.remove("d-none");
    SignUp[0].classList.add("d-none");
    SignUp[0].classList.remove("d-flex");
    Confirmation[0].classList.add("d-none");
    Confirmation[0].classList.remove("d-flex");
  }
}

function SignUpFun() {
  Login[0].classList.remove("d-flex");
  Login[0].classList.add("d-none");
  SignUp[0].classList.remove("d-none");
  SignUp[0].classList.add("d-flex");
  Confirmation[0].classList.remove("d-flex");
  Confirmation[0].classList.add("d-none");
}


// bottom navigation Bar
const navigationBottom = document.getElementsByClassName("navigationBottom");
function NavHandler(index) {
  for (let i = 0; i < navigationBottom.length; i++) {
    navigationBottom[i].classList.remove("navActive");
  }
  navigationBottom[index].classList.add("navActive");
  
}
