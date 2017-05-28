<template>
  <div id='app'>
    <LoginScreen
      v-if='!this.$store.state.loggedIn'
      :sendPasswordCallback='sendPassword'
    />
    <div v-else>
      <SqlPrompt :defaultQueryString='getDefaultQueryString()'/>
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
      getDefaultQueryString() {
        return 'select * from someTable'
      }
    }
  }

</script>

<style lang='scss' rel='stylesheet/scss'>
</style>
