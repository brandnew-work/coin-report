/* -------------------------------------------------------------------
  stickyContent
------------------------------------------------------------------- */

const stickyContent = (targetClass = [], scroll = 600) => {
  window.onscroll = () => {
    targetClass.forEach((target, i)=> {
      const el         = document.querySelector('.' + target[0])
      const body       = document.querySelector('.body')
      const classReady = 'scroll-ready'
      const classFixed = 'scroll-on'

      if (document.documentElement.scrollTop > (scroll - 400) || document.body.scrollTop > (scroll - 400)) {
        body.classList.add(classReady)
      } else {
        body.classList.remove(classReady)
      }
      if (document.documentElement.scrollTop > scroll || document.body.scrollTop > scroll) {
        body.classList.add(classFixed)
        if(targetClass[i][1] != '') {
          const el_hide    = document.querySelector('.' + targetClass[i][1])
          const el_hideRect= el_hide.getBoundingClientRect()
          if(el_hideRect.y > el_hideRect.height){
            body.classList.add(classFixed)
          } else {
            body.classList.remove(classFixed)
          }
        }
      } else {
        body.classList.remove(classFixed)
      }
    })
  }
}

export {stickyContent as default}
