import type { NextPage } from 'next'
import Head from 'next/head'
import styles from '../styles/Home.module.css'
import Header from '../components/header'
import { DataGrid, GridRowsProp, GridColDef } from '@mui/x-data-grid'
import Image from 'next/image'


const thumbnail = (params: any): JSX.Element => 
  <Image src={params.value} width='50px' height='50px' />
;

const columns: GridColDef[] = [
  { field: 'date', headerName: 'Date', type: 'date', flex: 1},
  { field: 'name', headerName: 'Name', type: 'string', flex: 1},
  { field: 'stack', headerName: 'Stack', type: 'string', flex: 1},
  { field: 'tech', headerName: 'Technology', type: 'array', flex: 1},
  { field: 'image', headerName: 'Image', type: 'image', renderCell: thumbnail, flex: 1},
];

const rows: GridRowsProp = [
  { id: 1, date: '01/08/2011', name: 'Anuvahood', stack: 'Mobile', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/anuvahood_aug_11.jpg' },
  { id: 2, date: '01/12/2011', name: 'Star Alliance', stack: 'Mobile', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/staralliance_dec_11.png' },
  { id: 3, date: '01/12/2012', name: 'Living Rating', stack: 'Frontend', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/living-ratings_dec_12.png' },
  { id: 4, date: '01/02/2013', name: 'indosgroup_feb_13', stack: 'Frontend', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/indosgroup_feb_13.png' },
  { id: 5, date: '01/03/2013', name: 'Living Group', stack: 'Email', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/lg-email_mar_13.png' },
  { id: 6, date: '01/03/2013', name: 'InvestSelect', stack: 'Frontend', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/investselect_mar_13.png' },
  { id: 7, date: '01/04/2013', name: 'Trident Trust', stack: 'Frontend', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/tridenttrust_apr_13.png' },
  { id: 8, date: '01/04/2013', name: 'Luqman Arnold', stack: 'Frontend', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/luqmanarnold_apr_13.png' },
  { id: 9, date: '01/05/2013', name: 'Living Group Email', stack: 'Email', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/lg-email_may_13.png' },
  { id: 10, date: '01/07/2013', name: 'Heptagon', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/heptagon_jul_13.png' },
  { id: 11, date: '01/07/2013', name: 'Higgs', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/higgs_jul_13.png' },
  { id: 12, date: '01/12/2013', name: 'Lansdowne Partners', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/lansdownepartner_dec_13.png' },
  { id: 13, date: '01/12/2013', name: 'Segantii', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/segantii_dec_13.png' },
  { id: 14, date: '01/01/2014', name: 'Bluematrix', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/bluematrix_jan_14.png' },
  { id: 15, date: '01/03/2014', name: 'Living Ratings', stack: 'Email', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/living-ratings-email_mar_14.png' },
  { id: 16, date: '01/03/2014', name: 'Trowe', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/trowe_mar_14.png' },
  { id: 17, date: '01/04/2014', name: 'Hayfin', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/hayfin_apr_14.png' },
  { id: 18, date: '01/05/2014', name: 'Cordellohome', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/cordellohome_may_14.png' },
  { id: 19, date: '01/10/2014', name: 'MSK Capital', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/mskcapital_oct_14.png' },
  { id: 20, date: '01/12/2014', name: 'Amiri Capital', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/amiri-capital_dec_14.png' },
  { id: 21, date: '01/12/2014', name: 'Chrome London', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/chrome-london_dec_14.png' },
  { id: 22, date: '01/12/2014', name: 'Living Ratings', stack: 'Email', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/living-ratings-email_mar_15.png' },
  { id: 23, date: '01/01/2015', name: 'Hearts of Home', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/hearts-of-home_jan_15.png' },
  { id: 24, date: '01/01/2015', name: 'Lansdowne Partners', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/lansdownepartners_jan_15.png' },
  { id: 25, date: '01/10/2016', name: 'Royal Opera House', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/roh_oct_16.png' },
  { id: 26, date: '01/11/2018', name: 'Royal Opera House', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/roh_nov_18.png' },
  { id: 27, date: '01/01/2022', name: 'Royal Opera House', stack: 'Fullstack', tech:['JS', 'Mobile', 'html', 'css'], image: '/portfolio/roh_jan_22.png' },
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
          <div style={{ height: 600, width: '100%' }}>
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
