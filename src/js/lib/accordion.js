/* -------------------------------------------------------------------
  accordion
------------------------------------------------------------------- */

const accordion = (animateDuration = 300, targetClass = '.accordion', target = 'next', type = 'click', flag = false) => {


  // slideUp
  const slideUp = (el, duration = 300) => {
    el.style.height = el.offsetHeight + "px"
    el.offsetHeight
    el.style.transitionProperty = "height, margin, padding"
    el.style.transitionDuration = duration + "ms"
    el.style.transitionTimingFunction = "ease"
    el.style.overflow = "hidden"
    el.style.height = 0
    el.style.paddingTop = 0
    el.style.paddingBottom = 0
    el.style.marginTop = 0
    el.style.marginBottom = 0
    setTimeout(() => {
      el.style.display = "none"
      el.style.removeProperty("height")
      el.style.removeProperty("padding-top")
      el.style.removeProperty("padding-bottom")
      el.style.removeProperty("margin-top")
      el.style.removeProperty("margin-bottom")
      el.style.removeProperty("overflow")
      el.style.removeProperty("transition-duration")
      el.style.removeProperty("transition-property")
      el.style.removeProperty("transition-timing-function")
    }, duration)
  }

  // slideDown
  const slideDown = (el, duration = 300) => {
    el.style.removeProperty("display")
    let display = window.getComputedStyle(el).display
    if (display === "none") {
      display = "block"
    }
    el.style.display = display
    let height = el.offsetHeight
    el.style.overflow = "hidden"
    el.style.height = 0
    el.style.paddingTop = 0
    el.style.paddingBottom = 0
    el.style.marginTop = 0
    el.style.marginBottom = 0
    el.offsetHeight
    el.style.transitionProperty = "height, margin, padding"
    el.style.transitionDuration = duration + "ms"
    el.style.transitionTimingFunction = "ease"
    el.style.height = height + "px"
    el.style.removeProperty("padding-top")
    el.style.removeProperty("padding-bottom")
    el.style.removeProperty("margin-top")
    el.style.removeProperty("margin-bottom")
    setTimeout(() => {
      el.style.removeProperty("height")
      el.style.removeProperty("overflow")
      el.style.removeProperty("transition-duration")
      el.style.removeProperty("transition-property")
      el.style.removeProperty("transition-timing-function")
    }, duration)
  }

  // slideToggle
  const slideToggle = (el, duration = 300) => {
    if (window.getComputedStyle(el).display === "none") {
      return slideDown(el, duration)
    } else {
      return slideUp(el, duration)
    }
  }

  // slideToggle
  document.querySelectorAll(targetClass).forEach((x) => {
    console.log(x)
    x.addEventListener(type, () => {
      if(target === 'next') {
        if(!flag) slideToggle(x.nextElementSibling, animateDuration)
      } else {
        if(!flag) slideToggle(x.previousElementSibling, animateDuration)
      }
      x.classList.toggle('--accordion-on')
    })
    if(type === 'mouseover') {
      if(target === 'next') {
        x.nextElementSibling.addEventListener('mouseleave', () => {
          if(!flag) slideToggle(x.nextElementSibling, animateDuration)
        })
      } else {
        x.previousElementSibling.addEventListener('mouseleave', () => {
          if(!flag) slideToggle(x.previousElementSibling, animateDuration)
        })
      }
      x.classList.toggle('--accordion-on')
    }
  })

}


export {accordion as default }
