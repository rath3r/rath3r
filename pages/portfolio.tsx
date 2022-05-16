import type { NextPage } from 'next'
import Head from 'next/head'
import styles from '../styles/Home.module.css'
import Header from '../components/header'
import { DataGrid, GridRowsProp, GridColDef } from '@mui/x-data-grid'
import Image from 'next/image'


 
// living-ratings_dec_12.png
// living-ratings-email-old.png
// living-ratings-email.png
// segantii_dec_13.png
// luqmanarnold_apr_13.png
// faceisyourfortune_july_13.png
// heptagon_jul_13.png
// higgs_jul_13.png
// indosgroup_feb_13.png
// investselect_mar_13.png
// lansdownepartner_dec_13.png
// tridenttrust_apr_13.png
// amiri-capital_dec_14.png
// bluematrix_jan_14.png
// chrome-london_dec_14.png
// cordellohome_may_14.png
// hayfin_apr_14.png
// mskcapital_oct_14.png
// segantii_dec_13.png
// trowe_mar_14.png
// hearts-of-home_jan_15.png
// lansdownepartners_jan_15.png
// lg-email_may_13.png
// lg-email_mar_13.png
// roh_oct_16.png
// roh_nov_18.png
// roh_jan_22.png
const columns: GridColDef[] = [
  { field: 'date', headerName: 'Date', type: 'date'},
  { field: 'name', headerName: 'Name', type: 'string'},
  { field: 'image', headerName: 'Image', type: 'image', renderCell: (params) => <Image src={params.value} width='50px' height='50px' />},
];

const rows: GridRowsProp = [
  { id: 1, date: '01/08/2011', name: 'Anuvahood', image: '/portfolio/anuvahood_aug_11.jpg' },
  { id: 2, date: '01/12/2011', name: 'Star Alliance', image: '/portfolio/staralliance_dec_11.png' },
  { id: 3, date: '01/12/2012', name: 'Living Rating', image: '/portfolio/living-ratings_dec_12.png' },
];


const Home: NextPage = () => {

  return (
    <>
      <Head>
        <title>rath3r</title>
        <meta name="description" content="The rath3r site"/>
        <meta name="author" content="Thomas Meehan"/>
        <link rel="icon" href="/favicon.ico?v=1" />
      </Head>
      <div className={styles.container}>
        <Header></Header>
        <main className={styles.main}>
          <div style={{ height: 300, width: '100%' }}>
            <DataGrid rows={rows} columns={columns} />
          </div>
        </main>
        <footer className={styles.footer}>
        </footer>
      </div>
    </>
  )
}

export default Home
