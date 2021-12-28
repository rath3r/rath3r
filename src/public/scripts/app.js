import LikeButton from './like_button';


const e = React.createElement;
// const like_button = Like_button;

const domContainer = document.querySelector('#like_button_container');
ReactDOM.render(e(LikeButton), domContainer);

console.log("hello???");