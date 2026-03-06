/* -------------------------------------------------------------------
  wp rest api fetch
------------------------------------------------------------------- */
import axios from 'axios'

const fetchClient = axios.create({
  baseURL: `//www.seiki.co.jp/tam/tamlog/wp-json/wp/v2/`,
})
// @type: posts, categories
const fetchDatas = async (type, params) => {
  const setParam = new URLSearchParams(params)
  const datas = fetchClient.get(type, {params: setParam})
  .then( res => (res.data) )
  const result = await datas
  return result
}
const getPosts = async (param = []) => {
  const baseParam = {
    per_page: 3,
    '_embed': '',
    '_fields': 'title, excerpt, link, categories, _embedded, _links', // _embedded取得に_linksが必要
  }
  const params = {...baseParam, ...param}
  const datas = await fetchDatas('posts', params)
  return datas
}
const getDatas = async () => {
  const relatedPosts = await getPosts()
  const popularPosts = await getPosts({tags: 12})
  return {relatedPosts, popularPosts}
}
const createColumn = async (data) => {
  const el_wrap = document.createElement('li')
  const el_inner = document.createElement('a')
  const el_figure = document.createElement('figure')
  const el_img = document.createElement('img')
  const el_title = document.createElement('p')
  const el_tags = document.createElement('div')
  el_wrap.classList.add('splide__slide')
  el_inner.classList.add('content-column')
  el_inner.classList.target = '_blank'
  el_figure.classList.add('content-column__figure')
  el_img.classList.add('content-column__img')
  el_title.classList.add('content-column__title')
  el_tags.classList.add('content-column__tags')
  el_inner.href = data.link
  el_title.textContent = data.title.rendered
  const embed = data._embedded
  el_img.src = embed['wp:featuredmedia'][0].source_url
  el_wrap.appendChild(el_inner)
  el_inner.appendChild(el_figure)
  el_figure.appendChild(el_img)
  el_inner.appendChild(el_title)
  el_inner.appendChild(el_tags)
  await embed['wp:term'][0].map((term) => {
    el_tags.appendChild(setTag(term.name))
  })
  return el_wrap
}
const setTag = (name) => {
  const el = document.createElement('span')
  el.classList.add('content-column__tag')
  el.textContent = `${name}`
  return el
}
const setData = async (_datas, target) => {
  const wrapper = document.querySelector(target)
  let columns = _datas.map( data => (createColumn(data)) )
  let datas = await Promise.all(columns)
  datas.map( el => wrapper.appendChild(el) )
}
const setDatas = async () => {
  const {relatedPosts, popularPosts} = await getDatas()
  await setData(popularPosts, '.js-popular-posts')
  await setData(relatedPosts, '.js-related-posts')
}

export {setDatas as default}
