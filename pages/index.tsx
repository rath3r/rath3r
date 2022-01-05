import type { NextPage } from 'next'
import Head from 'next/head'
import styles from '../styles/Home.module.css'

const Home: NextPage = () => {
  return (
    <div className={styles.container}>
      <Head>
        <title>rath3r</title>
        <meta name="description" content="The rath3r site"/>
        <meta name="author" content="Thomas Meehan"/>
        <link rel="icon" href="/favicon.ico?v=1" />
      </Head>

      <main className={styles.main}>
        <h1 className={styles.title}>
          Rath3r
        </h1>

        <p className={styles.description}>
            The portfolio site of a software engineer specialising in web applications. 
        </p>
        <ul>
          <li>Wouldn't you <a href="https://rath3r.com">rath3r?</a></li>
          <li>What would you <a href="https://rath3r.com">rath3r?</a></li>
        </ul>
          
      </main>

      <footer className={styles.footer}>
      </footer>
    </div>
  )
}

export default Home
