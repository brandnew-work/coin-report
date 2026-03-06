/* -------------------------------------------------------------------
  navigation
------------------------------------------------------------------- */

// navigation('アクションを起こすボタン', 'クラス付与先')
const navigation = (actionBtn, targetElement, callback) => {
  document.addEventListener('click', (x) => {
    const target = x.target
    if(callback) callback()
    setTimeout(()=>{
      if (target.classList.contains(actionBtn) || target.parentNode.classList.contains(actionBtn)) {
        document.querySelector('.' + targetElement).classList.toggle('--on')
      }
    }, 50)
  })
}

export {navigation as default}
