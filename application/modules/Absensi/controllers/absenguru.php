<?php

defined('BASEPATH') or exit('No direct script access allowed');

class absenguru extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('absenguru_m');
        $this->load->model('Mcrud');

        $this->load->library('form_validation');
    }

    public $titles = 'Absensi Guru';
    public $vn = 'absenguru';

    public function index()
    {
        $data['title'] = $this->titles;
        $data['pageTitle'] = "Data " . $this->titles;
        $data['data'] = $this->absenguru_m->getAllData();
        // $data['mapel'] = $this->absenguru_m->getMapel();

        $this->template->load('template', $this->vn . '/list', $data);
    }


    function add()
    {
        $data['title'] = $this->titles;
        $data['pageTitle'] = "Tambah Data " . $this->titles;
        $data['mapel'] = $this->absenguru_m->getMapel();
        $this->template->load('template', $this->vn . '/add', $data);
    }

    function detail()
    {
        $data['title'] = 'Absensi Kelas';
        $data['pageTitle'] = "Detail Data " . $this->titles;
        $id = $this->uri->segment(4);
        // $id2 = $this->uri->segment(5);
        $data['data'] = $this->absenguru_m->getSiswa($id);

        $this->template->load('template', $this->vn . '/detail', $data);
    }

    function addAction()
    {

        $this->absenguru_m->ok();
        redirect('absensi/' . $this->vn);
    }

    function edit()
    {
        $data['title'] = $this->titles;
        $data['pageTitle'] = "Edit Data " . $this->titles;
        $id = $this->uri->segment(4);
        $data['row'] = $this->absenguru_m->getDataById($id);
        $this->template->load('template', $this->vn . '/edit', $data);
    }

    function editAction()
    {
        $id = $this->uri->segment(4);
        $this->absenguru_m->update($id);
        redirect('absensi/' . $this->vn);
    }

    function delete()
    {
        $id = $this->uri->segment(4);
        $this->absenguru_m->delete($id);
        redirect('absensi/' . $this->vn);
    }

    function upload_foto()
    {
        $config['upload_path']          = './upload/';
        $config['allowed_types']        = 'jpg|png';
        $config['max_size']             = 1024; // imb
        $this->load->library('upload', $config);
        // proses upload
        $this->upload->do_upload('foto');
        $upload = $this->upload->data();
        return $upload['file_name'];
    }



    function getJadwalPerhari()
    {
        $tahun_akademik = $_GET['tahun_akademik'];
        $kelas = $_GET['kelas'];
        $this->db->where('jadwal_pelajaran.kodeTahun', $tahun_akademik);
        $this->db->where('jadwal_pelajaran.kodeKelas', $kelas);
        $this->db->join('tahun_akademik', 'tahun_akademik.kodeTahun = jadwal_pelajaran.kodeTahun', 'left');

        $this->db->join('kelas', 'kelas.kodeKelas = jadwal_pelajaran.kodeKelas', 'left');
        $this->db->join('mata_pelajaran', 'mata_pelajaran.kodeMapel = jadwal_pelajaran.kodeMapel', 'left');
        $this->db->join('guru', 'guru.nip = mata_pelajaran.nip', 'left');
        $data = $this->db->get('jadwal_pelajaran')->result();
        if (empty($kelas)) :
            echo '<h3 class="text-center"> Silahkan Pilih Hari Terlebih Dahulu </h3>';
        else :
            echo '<table id="basic-datatable" class="table dt-responsive nowrap">';
            echo '<thead>
        <tr>
            <th>No</th>
            <th>Hari</th>
            <th>Kelas</th>
            <th>Jam Mulai / Selesai</th>
            <th>Nama Pelajaran</th>
            <th>Nama Guru</th>
            <th>Aksi</th>

        </tr>
    </thead>';
            echo '<tbody>';
            $no = '1';
            foreach ($data as $row) :
                echo '<tr>
            <td> ' . $no++ . ' </td>
            <td> ' . $row->namaHari . ' </td>
            <td> ' . $row->namaKelas . ' </td>
            <td> ' . $row->jamMulai . '  /  ' . $row->jamSelesai . ' </td>
            <td> ' . $row->namaMapel . ' </td>
            <td> ' . $row->namaGuru . ' </td>
            <td>
                <div class="btn-group mb-0">
                <a href=" absenguru/' . $row->kodeJadwal . '/' . $row->nip . '" class="btn btn-info btn-sm" data-toggle="tooltip" title="Klik untuk Absensi"><i class="uil uil-clipboard"></i></a>
                    
                </div>
            </td>

        </tr>';
            endforeach;
            echo '</tbody>';
            echo '</table>';
        endif;
    }

    function getKehadiran()
    {

        $nip = $_GET['nip'];
        $kodeJadwal = $_GET['kodeJadwal'];
        $tanggal = $_GET['tanggal'];
        $this->db->where('nip', $nip);
        $this->db->where('kodeJadwal', $kodeJadwal);
        $this->db->where('tanggal', $tanggal);
        $cek = $this->db->get('absensi_guru')->num_rows();

        $object = [
            'idAbsenGuru' => uniqid(),
            'kodeJadwal' => $kodeJadwal,
            'nip' => $nip,
            'kehadiran' => 'H',
            'tanggal' => $tanggal,
        ];
        if ($cek <= 0) :
            if (empty($tanggal)) :
            else :
                $this->db->insert('absensi_guru', $object);
            endif;
        else :
            $this->db->where('nip', $nip);
            $this->db->where('kodeJadwal', $kodeJadwal);
            $this->db->where('tanggal', $tanggal);
            $this->db->update('absensi_guru', $object);
        endif;
    }
}

/* End of file */