import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const state = {
  loggedIn: false,
  currentQueryString: null,
  resultSet: null,
  favorites: [],
  queryHistory: [],
  tableList: [],
  autoCompleteOffer: null,
  renderStyle: 'table'
}

const mutations = {
  setLoggedIn(state, bool) {
    state.loggedIn = bool
  },
  setCurrentQueryString(state, str) {
    console.log('Setting query string to "' + str + '"')
    state.currentQueryString = str
  },
  setResultSet(state, resultSet) {
    state.resultSet = resultSet
  },
  addFavorite(state, queryString) {
    state.favorites.push(queryString)
  },
  deleteLastFavorite(state) {
    state.favorites.shift()
  },
  addToQueryHistory(state, queryString) {
    state.queryHistory.push(queryString)
  },
  populateTableList(state, tables) {
    state.tableList = tables
  },
  setAutoCompleteOffer(state, str) {
    state.autoCompleteOffer = str
  },
  setRenderStyle(state, styleString) {
    state.renderStyle = styleString
  }
}

export default new Vuex.Store({
  state,
  mutations
})
