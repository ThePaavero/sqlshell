<template>
  <div id='app'
    @keydown.ctrl.13.prevent='dispatch("run-query")'
    @keydown.ctrl.27.prevent='dispatch("focus-on-prompt")'
    @keydown.ctrl.83.prevent='dispatch("add-current-query-to-favorites")'
    @keydown.ctrl.69.prevent='dispatch("download-dump")'
    @keydown.ctrl.66.prevent='dispatch("toggle-tables-section")'
    @keydown.ctrl.76.prevent='dispatch("toggle-favorites-section")'
    @keydown.ctrl.68.prevent='dispatch("delete-last-favorite")'
  >
    <LoginScreen
      v-if='!this.$store.state.loggedIn'
      :sendPasswordCallback='sendPassword'
    />
    <div v-else>
      <SqlPrompt :defaultQueryString='getDefaultQueryString()' ref='sqlPrompt'/>
      <ActionList/>
    </div>
  </div>
</template>

<script>
  import LoginScreen from './components/LoginScreen.vue'
  import ActionList from './components/ActionList.vue'
  import FavoritesList from './components/FavoritesList.vue'
  import ResultDisplay from './components/ResultDisplay.vue'
  import SqlPrompt from './components/SqlPrompt.vue'
  import TableList from './components/TableList.vue'
  import axios from 'axios'
  //  axios.defaults.withCredentials = true

  const apiBaseUrl = 'http://sqlshell.dev:8000/'

  export default {
    name: 'app',
    components: {
      LoginScreen,
      ActionList,
      FavoritesList,
      ResultDisplay,
      SqlPrompt,
      TableList,
    },
    mounted() {
      axios.get(apiBaseUrl + '?ajax=1&action=GIVE-LOGGED-IN-STATUS')
        .then(response => {
          if (response.data.loggedIn) {
            this.$store.commit('setLoggedIn', true)
          } else {
            this.$store.commit('setLoggedIn', false)
          }
        })
        .catch(console.error)
    },
    methods: {
      sendPassword(e) {
        const password = e.currentTarget.password.value
        const params = new window.FormData()
        params.append('password', password)
        axios.post(apiBaseUrl + '?ajax=1&action=LOGIN', params)
          .then(response => {
            if (response.data.error) {
              window.alert(response.data.error)
              return
            }
            this.$store.commit('setLoggedIn', true)
          })
          .catch(console.error)
      },
      focusOnPrompt() {
        console.log('focusing')
        this.$refs.sqlPrompt.$el.focus()
      },
      getDefaultQueryString() {
        return 'select * from someTable'
      },
      dispatch(actionKey) {
        switch (actionKey) {
          case 'focus-on-prompt':
            this.focusOnPrompt()
            break
        }
      }
    }
  }

</script>

<style lang='scss' rel='stylesheet/scss'>
</style>
