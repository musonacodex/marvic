<?php

namespace Marvic\HTTP;

/**
 * List of supported mime types by the marvic Framework
 * 
 * @package Marvic\HTTP
 * @version 1.0.0
 */
final class MimeTypes {
	/** @var array<string, array> */
	private static array $collection = [
		'x3d' => [
			'name' => '3D Crossword Plugin',
			'type' => 'application/vnd.hzn-3d-crossword'
		],
		'3gp' => [
			'name' => '3GP',
			'type' => 'video/3gpp'
		],
		'3g2' => [
			'name' => '3GP2',
			'type' => 'video/3gpp2'
		],
		'mseq' => [
			'name' => '3GPP MSEQ File',
			'type' => 'application/vnd.mseq'
		],
		'pwn' => [
			'name' => '3M Post It Notes',
			'type' => 'application/vnd.3m.post-it-notes'
		],
		'plb' => [
			'name' => '3rd Generation Partnership Project - Pic Large',
			'type' => 'application/vnd.3gpp.pic-bw-large'
		],
		'psb' => [
			'name' => '3rd Generation Partnership Project - Pic Small',
			'type' => 'application/vnd.3gpp.pic-bw-small'
		],
		'pvb' => [
			'name' => '3rd Generation Partnership Project - Pic Var',
			'type' => 'application/vnd.3gpp.pic-bw-var'
		],
		'tcap' => [
			'name' => '3rd Generation Partnership Project - Transaction Capabilities Application Part',
			'type' => 'application/vnd.3gpp2.tcap'
		],
		'7z' => [
			'name' => '7-Zip',
			'type' => 'application/x-7z-compressed'
		],
		'abw' => [
			'name' => 'AbiWord',
			'type' => 'application/x-abiword'
		],
		'ace' => [
			'name' => 'Ace Archive',
			'type' => 'application/x-ace-compressed'
		],
		'acc' => [
			'name' => 'Active Content Compression',
			'type' => 'application/vnd.americandynamics.acc'
		],
		'acu' => [
			'name' => 'ACU Cobol',
			'type' => 'application/vnd.acucobol'
		],
		'atc' => [
			'name' => 'ACU Cobol',
			'type' => 'application/vnd.acucorp'
		],
		'adp' => [
			'name' => 'Adaptive differential pulse-code modulation',
			'type' => 'audio/adpcm'
		],
		'aab' => [
			'name' => 'Adobe (Macropedia) Authorware - Binary File',
			'type' => 'application/x-authorware-bin'
		],
		'aam' => [
			'name' => 'Adobe (Macropedia) Authorware - Map',
			'type' => 'application/x-authorware-map'
		],
		'aas' => [
			'name' => 'Adobe (Macropedia) Authorware - Segment File',
			'type' => 'application/x-authorware-seg'
		],
		'air' => [
			'name' => 'Adobe AIR Application',
			'type' => 'application/vnd.adobe.air-application-installer-package+zip'
		],
		'swf' => [
			'name' => 'Adobe Flash',
			'type' => 'application/x-shockwave-flash'
		],
		'fxp' => [
			'name' => 'Adobe Flex Project',
			'type' => 'application/vnd.adobe.fxp'
		],
		'pdf' => [
			'name' => 'Adobe Portable Document Format',
			'type' => 'application/pdf'
		],
		'ppd' => [
			'name' => 'Adobe PostScript Printer Description File Format',
			'type' => 'application/vnd.cups-ppd'
		],
		'dir' => [
			'name' => 'Adobe Shockwave Player',
			'type' => 'application/x-director'
		],
		'xdp' => [
			'name' => 'Adobe XML Data Package',
			'type' => 'application/vnd.adobe.xdp+xml'
		],
		'xfdf' => [
			'name' => 'Adobe XML Forms Data Format',
			'type' => 'application/vnd.adobe.xfdf'
		],
		'aac' => [
			'name' => 'Advanced Audio Coding (AAC)',
			'type' => 'audio/x-aac'
		],
		'ahead' => [
			'name' => 'Ahead AIR Application',
			'type' => 'application/vnd.ahead.space'
		],
		'azf' => [
			'name' => 'AirZip FileSECURE',
			'type' => 'application/vnd.airzip.filesecure.azf'
		],
		'azs' => [
			'name' => 'AirZip FileSECURE',
			'type' => 'application/vnd.airzip.filesecure.azs'
		],
		'azw' => [
			'name' => 'Amazon Kindle eBook format',
			'type' => 'application/vnd.amazon.ebook'
		],
		'ami' => [
			'name' => 'AmigaDE',
			'type' => 'application/vnd.amiga.ami'
		],
		'N/A' => [
			'name' => 'Andrew Toolkit',
			'type' => 'application/andrew-inset'
		],
		'apk' => [
			'name' => 'Android Package Archive',
			'type' => 'application/vnd.android.package-archive'
		],
		'cii' => [
			'name' => 'ANSER-WEB Terminal Client - Certificate Issue',
			'type' => 'application/vnd.anser-web-certificate-issue-initiation'
		],
		'fti' => [
			'name' => 'ANSER-WEB Terminal Client - Web Funds Transfer',
			'type' => 'application/vnd.anser-web-funds-transfer-initiation'
		],
		'atx' => [
			'name' => 'Antix Game Player',
			'type' => 'application/vnd.antix.game-component'
		],
		'mpkg' => [
			'name' => 'Apple Installer Package',
			'type' => 'application/vnd.apple.installer+xml'
		],
		'aw' => [
			'name' => 'Applixware',
			'type' => 'application/applixware'
		],
		'les' => [
			'name' => 'Archipelago Lesson Player',
			'type' => 'application/vnd.hhe.lesson-player'
		],
		'swi' => [
			'name' => 'Arista Networks Software Image',
			'type' => 'application/vnd.aristanetworks.swi'
		],
		's' => [
			'name' => 'Assembler Source File',
			'type' => 'text/x-asm'
		],
		'atomcat' => [
			'name' => 'Atom Publishing Protocol',
			'type' => 'application/atomcat+xml'
		],
		'atomsvc' => [
			'name' => 'Atom Publishing Protocol Service Document',
			'type' => 'application/atomsvc+xml'
		],
		'".atom, .xml"' => [
			'name' => 'Atom Syndication Format',
			'type' => 'application/atom+xml'
		],
		'ac' => [
			'name' => 'Attribute Certificate',
			'type' => 'application/pkix-attr-cert'
		],
		'aif' => [
			'name' => 'Audio Interchange File Format',
			'type' => 'audio/x-aiff'
		],
		'avi' => [
			'name' => 'Audio Video Interleave (AVI)',
			'type' => 'video/x-msvideo'
		],
		'aep' => [
			'name' => 'Audiograph',
			'type' => 'application/vnd.audiograph'
		],
		'dxf' => [
			'name' => 'AutoCAD DXF',
			'type' => 'image/vnd.dxf'
		],
		'dwf' => [
			'name' => 'Autodesk Design Web Format (DWF)',
			'type' => 'model/vnd.dwf'
		],
		'bcpio' => [
			'name' => 'Binary CPIO Archive',
			'type' => 'application/x-bcpio'
		],
		'bin' => [
			'name' => 'Binary Data',
			'type' => 'application/octet-stream'
		],
		'bmp' => [
			'name' => 'Bitmap Image File',
			'type' => 'image/bmp'
		],
		'torrent' => [
			'name' => 'BitTorrent',
			'type' => 'application/x-bittorrent'
		],
		'cod' => [
			'name' => 'Blackberry COD File',
			'type' => 'application/vnd.rim.cod'
		],
		'mpm' => [
			'name' => 'Blueice Research Multipass',
			'type' => 'application/vnd.blueice.multipass'
		],
		'bmi' => [
			'name' => 'BMI Drawing Data Interchange',
			'type' => 'application/vnd.bmi'
		],
		'sh' => [
			'name' => 'Bourne Shell Script',
			'type' => 'application/x-sh'
		],
		'btif' => [
			'name' => 'BTIF',
			'type' => 'image/prs.btif'
		],
		'rep' => [
			'name' => 'BusinessObjects',
			'type' => 'application/vnd.businessobjects'
		],
		'bz' => [
			'name' => 'Bzip Archive',
			'type' => 'application/x-bzip'
		],
		'bz2' => [
			'name' => 'Bzip2 Archive',
			'type' => 'application/x-bzip2'
		],
		'csh' => [
			'name' => 'C Shell Script',
			'type' => 'application/x-csh'
		],
		'c' => [
			'name' => 'C Source File',
			'type' => 'text/x-c'
		],
		'cdxml' => [
			'name' => 'CambridgeSoft Chem Draw',
			'type' => 'application/vnd.chemdraw+xml'
		],
		'css' => [
			'name' => 'Cascading Style Sheets (CSS)',
			'type' => 'text/css'
		],
		'cdx' => [
			'name' => 'ChemDraw eXchange file',
			'type' => 'chemical/x-cdx'
		],
		'cml' => [
			'name' => 'Chemical Markup Language',
			'type' => 'chemical/x-cml'
		],
		'csml' => [
			'name' => 'Chemical Style Markup Language',
			'type' => 'chemical/x-csml'
		],
		'cdbcmsg' => [
			'name' => 'CIM Database',
			'type' => 'application/vnd.contact.cmsg'
		],
		'cla' => [
			'name' => 'Claymore Data Files',
			'type' => 'application/vnd.claymore'
		],
		'c4g' => [
			'name' => 'Clonk Game',
			'type' => 'application/vnd.clonk.c4group'
		],
		'sub' => [
			'name' => 'Close Captioning - Subtitle',
			'type' => 'image/vnd.dvb.subtitle'
		],
		'cdmia' => [
			'name' => 'Cloud Data Management Interface (CDMI) - Capability',
			'type' => 'application/cdmi-capability'
		],
		'cdmic' => [
			'name' => 'Cloud Data Management Interface (CDMI) - Contaimer',
			'type' => 'application/cdmi-container'
		],
		'cdmid' => [
			'name' => 'Cloud Data Management Interface (CDMI) - Domain',
			'type' => 'application/cdmi-domain'
		],
		'cdmio' => [
			'name' => 'Cloud Data Management Interface (CDMI) - Object',
			'type' => 'application/cdmi-object'
		],
		'cdmiq' => [
			'name' => 'Cloud Data Management Interface (CDMI) - Queue',
			'type' => 'application/cdmi-queue'
		],
		'c11amc' => [
			'name' => 'ClueTrust CartoMobile - Config',
			'type' => 'application/vnd.cluetrust.cartomobile-config'
		],
		'c11amz' => [
			'name' => 'ClueTrust CartoMobile - Config Package',
			'type' => 'application/vnd.cluetrust.cartomobile-config-pkg'
		],
		'ras' => [
			'name' => 'CMU Image',
			'type' => 'image/x-cmu-raster'
		],
		'dae' => [
			'name' => 'COLLADA',
			'type' => 'model/vnd.collada+xml'
		],
		'csv' => [
			'name' => 'Comma-Seperated Values',
			'type' => 'text/csv'
		],
		'cpt' => [
			'name' => 'Compact Pro',
			'type' => 'application/mac-compactpro'
		],
		'wmlc' => [
			'name' => 'Compiled Wireless Markup Language (WMLC)',
			'type' => 'application/vnd.wap.wmlc'
		],
		'cgm' => [
			'name' => 'Computer Graphics Metafile',
			'type' => 'image/cgm'
		],
		'ice' => [
			'name' => 'CoolTalk',
			'type' => 'x-conference/x-cooltalk'
		],
		'cmx' => [
			'name' => 'Corel Metafile Exchange (CMX)',
			'type' => 'image/x-cmx'
		],
		'xar' => [
			'name' => 'CorelXARA',
			'type' => 'application/vnd.xara'
		],
		'cmc' => [
			'name' => 'CosmoCaller',
			'type' => 'application/vnd.cosmocaller'
		],
		'cpio' => [
			'name' => 'CPIO Archive',
			'type' => 'application/x-cpio'
		],
		'clkx' => [
			'name' => 'CrickSoftware - Clicker',
			'type' => 'application/vnd.crick.clicker'
		],
		'clkk' => [
			'name' => 'CrickSoftware - Clicker - Keyboard',
			'type' => 'application/vnd.crick.clicker.keyboard'
		],
		'clkp' => [
			'name' => 'CrickSoftware - Clicker - Palette',
			'type' => 'application/vnd.crick.clicker.palette'
		],
		'clkt' => [
			'name' => 'CrickSoftware - Clicker - Template',
			'type' => 'application/vnd.crick.clicker.template'
		],
		'clkw' => [
			'name' => 'CrickSoftware - Clicker - Wordbank',
			'type' => 'application/vnd.crick.clicker.wordbank'
		],
		'wbs' => [
			'name' => 'Critical Tools - PERT Chart EXPERT',
			'type' => 'application/vnd.criticaltools.wbs+xml'
		],
		'cryptonote' => [
			'name' => 'CryptoNote',
			'type' => 'application/vnd.rig.cryptonote'
		],
		'cif' => [
			'name' => 'Crystallographic Interchange Format',
			'type' => 'chemical/x-cif'
		],
		'cmdf' => [
			'name' => 'CrystalMaker Data Format',
			'type' => 'chemical/x-cmdf'
		],
		'cu' => [
			'name' => 'CU-SeeMe',
			'type' => 'application/cu-seeme'
		],
		'cww' => [
			'name' => 'CU-Writer',
			'type' => 'application/prs.cww'
		],
		'curl' => [
			'name' => 'Curl - Applet',
			'type' => 'text/vnd.curl'
		],
		'dcurl' => [
			'name' => 'Curl - Detached Applet',
			'type' => 'text/vnd.curl.dcurl'
		],
		'mcurl' => [
			'name' => 'Curl - Manifest File',
			'type' => 'text/vnd.curl.mcurl'
		],
		'scurl' => [
			'name' => 'Curl - Source Code',
			'type' => 'text/vnd.curl.scurl'
		],
		'car' => [
			'name' => 'CURL Applet',
			'type' => 'application/vnd.curl.car'
		],
		'pcurl' => [
			'name' => 'CURL Applet',
			'type' => 'application/vnd.curl.pcurl'
		],
		'cmp' => [
			'name' => 'CustomMenu',
			'type' => 'application/vnd.yellowriver-custom-menu'
		],
		'dssc' => [
			'name' => 'Data Structure for the Security Suitability of Cryptographic Algorithms',
			'type' => 'application/dssc+der'
		],
		'xdssc' => [
			'name' => 'Data Structure for the Security Suitability of Cryptographic Algorithms',
			'type' => 'application/dssc+xml'
		],
		'deb' => [
			'name' => 'Debian Package',
			'type' => 'application/x-debian-package'
		],
		'uva' => [
			'name' => 'DECE Audio',
			'type' => 'audio/vnd.dece.audio'
		],
		'uvi' => [
			'name' => 'DECE Graphic',
			'type' => 'image/vnd.dece.graphic'
		],
		'uvh' => [
			'name' => 'DECE High Definition Video',
			'type' => 'video/vnd.dece.hd'
		],
		'uvm' => [
			'name' => 'DECE Mobile Video',
			'type' => 'video/vnd.dece.mobile'
		],
		'uvu' => [
			'name' => 'DECE MP4',
			'type' => 'video/vnd.uvvu.mp4'
		],
		'uvp' => [
			'name' => 'DECE PD Video',
			'type' => 'video/vnd.dece.pd'
		],
		'uvs' => [
			'name' => 'DECE SD Video',
			'type' => 'video/vnd.dece.sd'
		],
		'uaa' => [
			'name' => 'DECE Video',
			'type' => 'video/vnd.dece.video'
		],
		'dvi' => [
			'name' => 'Device Independent File Format (DVI)',
			'type' => 'application/x-dvi'
		],
		'seed' => [
			'name' => 'Digital Siesmograph Networks - SEED Datafiles',
			'type' => 'application/vnd.fdsn.seed'
		],
		'dtb' => [
			'name' => 'Digital Talking Book',
			'type' => 'application/x-dtbook+xml'
		],
		'res' => [
			'name' => 'Digital Talking Book - Resource File',
			'type' => 'application/x-dtbresource+xml'
		],
		'ait' => [
			'name' => 'Digital Video Broadcasting',
			'type' => 'application/vnd.dvb.ait'
		],
		'svc' => [
			'name' => 'Digital Video Broadcasting',
			'type' => 'application/vnd.dvb.service'
		],
		'eol' => [
			'name' => 'Digital Winds Music',
			'type' => 'audio/vnd.digital-winds'
		],
		'djvu' => [
			'name' => 'DjVu',
			'type' => 'image/vnd.djvu'
		],
		'dtd' => [
			'name' => 'Document Type Definition',
			'type' => 'application/xml-dtd'
		],
		'mlp' => [
			'name' => 'Dolby Meridian Lossless Packing',
			'type' => 'application/vnd.dolby.mlp'
		],
		'wad' => [
			'name' => 'Doom Video Game',
			'type' => 'application/x-doom'
		],
		'dpg' => [
			'name' => 'DPGraph',
			'type' => 'application/vnd.dpgraph'
		],
		'dra' => [
			'name' => 'DRA Audio',
			'type' => 'audio/vnd.dra'
		],
		'dfac' => [
			'name' => 'DreamFactory',
			'type' => 'application/vnd.dreamfactory'
		],
		'dts' => [
			'name' => 'DTS Audio',
			'type' => 'audio/vnd.dts'
		],
		'dtshd' => [
			'name' => 'DTS High Definition Audio',
			'type' => 'audio/vnd.dts.hd'
		],
		'dwg' => [
			'name' => 'DWG Drawing',
			'type' => 'image/vnd.dwg'
		],
		'geo' => [
			'name' => 'DynaGeo',
			'type' => 'application/vnd.dynageo'
		],
		'es' => [
			'name' => 'ECMAScript',
			'type' => 'application/ecmascript'
		],
		'mag' => [
			'name' => 'EcoWin Chart',
			'type' => 'application/vnd.ecowin.chart'
		],
		'mmr' => [
			'name' => 'EDMICS 2000',
			'type' => 'image/vnd.fujixerox.edmics-mmr'
		],
		'rlc' => [
			'name' => 'EDMICS 2000',
			'type' => 'image/vnd.fujixerox.edmics-rlc'
		],
		'exi' => [
			'name' => 'Efficient XML Interchange',
			'type' => 'application/exi'
		],
		'mgz' => [
			'name' => 'EFI Proteus',
			'type' => 'application/vnd.proteus.magazine'
		],
		'epub' => [
			'name' => 'Electronic Publication',
			'type' => 'application/epub+zip'
		],
		'eml' => [
			'name' => 'Email Message',
			'type' => 'message/rfc822'
		],
		'nml' => [
			'name' => 'Enliven Viewer',
			'type' => 'application/vnd.enliven'
		],
		'xpr' => [
			'name' => 'Express by Infoseek',
			'type' => 'application/vnd.is-xpr'
		],
		'xif' => [
			'name' => 'eXtended Image File Format (XIFF)',
			'type' => 'image/vnd.xiff'
		],
		'xfdl' => [
			'name' => 'Extensible Forms Description Language',
			'type' => 'application/vnd.xfdl'
		],
		'emma' => [
			'name' => 'Extensible MultiModal Annotation',
			'type' => 'application/emma+xml'
		],
		'ez2' => [
			'name' => 'EZPix Secure Photo Album',
			'type' => 'application/vnd.ezpix-album'
		],
		'ez3' => [
			'name' => 'EZPix Secure Photo Album',
			'type' => 'application/vnd.ezpix-package'
		],
		'fst' => [
			'name' => 'FAST Search & Transfer ASA',
			'type' => 'image/vnd.fst'
		],
		'fvt' => [
			'name' => 'FAST Search & Transfer ASA',
			'type' => 'video/vnd.fvt'
		],
		'fbs' => [
			'name' => 'FastBid Sheet',
			'type' => 'image/vnd.fastbidsheet'
		],
		'fe_launch' => [
			'name' => 'FCS Express Layout Link',
			'type' => 'application/vnd.denovo.fcselayout-link'
		],
		'f4v' => [
			'name' => 'Flash Video',
			'type' => 'video/x-f4v'
		],
		'flv' => [
			'name' => 'Flash Video',
			'type' => 'video/x-flv'
		],
		'fpx' => [
			'name' => 'FlashPix',
			'type' => 'image/vnd.fpx'
		],
		'npx' => [
			'name' => 'FlashPix',
			'type' => 'image/vnd.net-fpx'
		],
		'flx' => [
			'name' => 'FLEXSTOR',
			'type' => 'text/vnd.fmi.flexstor'
		],
		'fli' => [
			'name' => 'FLI/FLC Animation Format',
			'type' => 'video/x-fli'
		],
		'ftc' => [
			'name' => 'FluxTime Clip',
			'type' => 'application/vnd.fluxtime.clip'
		],
		'fdf' => [
			'name' => 'Forms Data Format',
			'type' => 'application/vnd.fdf'
		],
		'f' => [
			'name' => 'Fortran Source File',
			'type' => 'text/x-fortran'
		],
		'mif' => [
			'name' => 'FrameMaker Interchange Format',
			'type' => 'application/vnd.mif'
		],
		'fm' => [
			'name' => 'FrameMaker Normal Format',
			'type' => 'application/vnd.framemaker'
		],
		'fh' => [
			'name' => 'FreeHand MX',
			'type' => 'image/x-freehand'
		],
		'fsc' => [
			'name' => 'Friendly Software Corporation',
			'type' => 'application/vnd.fsc.weblaunch'
		],
		'fnc' => [
			'name' => 'Frogans Player',
			'type' => 'application/vnd.frogans.fnc'
		],
		'ltf' => [
			'name' => 'Frogans Player',
			'type' => 'application/vnd.frogans.ltf'
		],
		'ddd' => [
			'name' => 'Fujitsu - Xerox 2D CAD Data',
			'type' => 'application/vnd.fujixerox.ddd'
		],
		'xdw' => [
			'name' => 'Fujitsu - Xerox DocuWorks',
			'type' => 'application/vnd.fujixerox.docuworks'
		],
		'xbd' => [
			'name' => 'Fujitsu - Xerox DocuWorks Binder',
			'type' => 'application/vnd.fujixerox.docuworks.binder'
		],
		'oas' => [
			'name' => 'Fujitsu Oasys',
			'type' => 'application/vnd.fujitsu.oasys'
		],
		'oa2' => [
			'name' => 'Fujitsu Oasys',
			'type' => 'application/vnd.fujitsu.oasys2'
		],
		'oa3' => [
			'name' => 'Fujitsu Oasys',
			'type' => 'application/vnd.fujitsu.oasys3'
		],
		'fg5' => [
			'name' => 'Fujitsu Oasys',
			'type' => 'application/vnd.fujitsu.oasysgp'
		],
		'bh2' => [
			'name' => 'Fujitsu Oasys',
			'type' => 'application/vnd.fujitsu.oasysprs'
		],
		'spl' => [
			'name' => 'FutureSplash Animator',
			'type' => 'application/x-futuresplash'
		],
		'fzs' => [
			'name' => 'FuzzySheet',
			'type' => 'application/vnd.fuzzysheet'
		],
		'g3' => [
			'name' => 'G3 Fax Image',
			'type' => 'image/g3fax'
		],
		'gmx' => [
			'name' => 'GameMaker ActiveX',
			'type' => 'application/vnd.gmx'
		],
		'gtw' => [
			'name' => 'Gen-Trix Studio',
			'type' => 'model/vnd.gtw'
		],
		'txd' => [
			'name' => 'Genomatix Tuxedo Framework',
			'type' => 'application/vnd.genomatix.tuxedo'
		],
		'ggb' => [
			'name' => 'GeoGebra',
			'type' => 'application/vnd.geogebra.file'
		],
		'ggt' => [
			'name' => 'GeoGebra',
			'type' => 'application/vnd.geogebra.tool'
		],
		'gdl' => [
			'name' => 'Geometric Description Language (GDL)',
			'type' => 'model/vnd.gdl'
		],
		'gex' => [
			'name' => 'GeoMetry Explorer',
			'type' => 'application/vnd.geometry-explorer'
		],
		'gxt' => [
			'name' => 'GEONExT and JSXGraph',
			'type' => 'application/vnd.geonext'
		],
		'g2w' => [
			'name' => 'GeoplanW',
			'type' => 'application/vnd.geoplan'
		],
		'g3w' => [
			'name' => 'GeospacW',
			'type' => 'application/vnd.geospace'
		],
		'gsf' => [
			'name' => 'Ghostscript Font',
			'type' => 'application/x-font-ghostscript'
		],
		'bdf' => [
			'name' => 'Glyph Bitmap Distribution Format',
			'type' => 'application/x-font-bdf'
		],
		'gtar' => [
			'name' => 'GNU Tar Files',
			'type' => 'application/x-gtar'
		],
		'texinfo' => [
			'name' => 'GNU Texinfo Document',
			'type' => 'application/x-texinfo'
		],
		'gnumeric' => [
			'name' => 'Gnumeric',
			'type' => 'application/x-gnumeric'
		],
		'kml' => [
			'name' => 'Google Earth - KML',
			'type' => 'application/vnd.google-earth.kml+xml'
		],
		'kmz' => [
			'name' => 'Google Earth - Zipped KML',
			'type' => 'application/vnd.google-earth.kmz'
		],
		'gqf' => [
			'name' => 'GrafEq',
			'type' => 'application/vnd.grafeq'
		],
		'gif' => [
			'name' => 'Graphics Interchange Format',
			'type' => 'image/gif'
		],
		'gv' => [
			'name' => 'Graphviz',
			'type' => 'text/vnd.graphviz'
		],
		'gac' => [
			'name' => 'Groove - Account',
			'type' => 'application/vnd.groove-account'
		],
		'ghf' => [
			'name' => 'Groove - Help',
			'type' => 'application/vnd.groove-help'
		],
		'gim' => [
			'name' => 'Groove - Identity Message',
			'type' => 'application/vnd.groove-identity-message'
		],
		'grv' => [
			'name' => 'Groove - Injector',
			'type' => 'application/vnd.groove-injector'
		],
		'gtm' => [
			'name' => 'Groove - Tool Message',
			'type' => 'application/vnd.groove-tool-message'
		],
		'tpl' => [
			'name' => 'Groove - Tool Template',
			'type' => 'application/vnd.groove-tool-template'
		],
		'vcg' => [
			'name' => 'Groove - Vcard',
			'type' => 'application/vnd.groove-vcard'
		],
		'h261' => [
			'name' => 'H.261',
			'type' => 'video/h261'
		],
		'h263' => [
			'name' => 'H.263',
			'type' => 'video/h263'
		],
		'h264' => [
			'name' => 'H.264',
			'type' => 'video/h264'
		],
		'hpid' => [
			'name' => 'Hewlett Packard Instant Delivery',
			'type' => 'application/vnd.hp-hpid'
		],
		'hps' => [
			'name' => 'Hewlett-Packard\'s WebPrintSmart',
			'type' => 'application/vnd.hp-hps'
		],
		'hdf' => [
			'name' => 'Hierarchical Data Format',
			'type' => 'application/x-hdf'
		],
		'rip' => [
			'name' => 'Hit\'n\'Mix',
			'type' => 'audio/vnd.rip'
		],
		'hbci' => [
			'name' => 'Homebanking Computer Interface (HBCI)',
			'type' => 'application/vnd.hbci'
		],
		'jlt' => [
			'name' => 'HP Indigo Digital Press - Job Layout Languate',
			'type' => 'application/vnd.hp-jlyt'
		],
		'pcl' => [
			'name' => 'HP Printer Command Language',
			'type' => 'application/vnd.hp-pcl'
		],
		'hpgl' => [
			'name' => 'HP-GL/2 and HP RTL',
			'type' => 'application/vnd.hp-hpgl'
		],
		'hvs' => [
			'name' => 'HV Script',
			'type' => 'application/vnd.yamaha.hv-script'
		],
		'hvd' => [
			'name' => 'HV Voice Dictionary',
			'type' => 'application/vnd.yamaha.hv-dic'
		],
		'hvp' => [
			'name' => 'HV Voice Parameter',
			'type' => 'application/vnd.yamaha.hv-voice'
		],
		'sfd-hdstx' => [
			'name' => 'Hydrostatix Master Suite',
			'type' => 'application/vnd.hydrostatix.sof-data'
		],
		'stk' => [
			'name' => 'Hyperstudio',
			'type' => 'application/hyperstudio'
		],
		'hal' => [
			'name' => 'Hypertext Application Language',
			'type' => 'application/vnd.hal+xml'
		],
		'html' => [
			'name' => 'HyperText Markup Language (HTML)',
			'type' => 'text/html'
		],
		'irm' => [
			'name' => 'IBM DB2 Rights Manager',
			'type' => 'application/vnd.ibm.rights-management'
		],
		'sc' => [
			'name' => 'IBM Electronic Media Management System - Secure Container',
			'type' => 'application/vnd.ibm.secure-container'
		],
		'ics' => [
			'name' => 'iCalendar',
			'type' => 'text/calendar'
		],
		'icc' => [
			'name' => 'ICC profile',
			'type' => 'application/vnd.iccprofile'
		],
		'ico' => [
			'name' => 'Icon Image',
			'type' => 'image/x-icon'
		],
		'igl' => [
			'name' => 'igLoader',
			'type' => 'application/vnd.igloader'
		],
		'ief' => [
			'name' => 'Image Exchange Format',
			'type' => 'image/ief'
		],
		'ivp' => [
			'name' => 'ImmerVision PURE Players',
			'type' => 'application/vnd.immervision-ivp'
		],
		'ivu' => [
			'name' => 'ImmerVision PURE Players',
			'type' => 'application/vnd.immervision-ivu'
		],
		'rif' => [
			'name' => 'IMS Networks',
			'type' => 'application/reginfo+xml'
		],
		'3dml' => [
			'name' => 'In3D - 3DML',
			'type' => 'text/vnd.in3d.3dml'
		],
		'spot' => [
			'name' => 'In3D - 3DML',
			'type' => 'text/vnd.in3d.spot'
		],
		'igs' => [
			'name' => 'Initial Graphics Exchange Specification (IGES)',
			'type' => 'model/iges'
		],
		'i2g' => [
			'name' => 'Interactive Geometry Software',
			'type' => 'application/vnd.intergeo'
		],
		'cdy' => [
			'name' => 'Interactive Geometry Software Cinderella',
			'type' => 'application/vnd.cinderella'
		],
		'xpw' => [
			'name' => 'Intercon FormNet',
			'type' => 'application/vnd.intercon.formnet'
		],
		'fcs' => [
			'name' => 'International Society for Advancement of Cytometry',
			'type' => 'application/vnd.isac.fcs'
		],
		'ipfix' => [
			'name' => 'Internet Protocol Flow Information Export',
			'type' => 'application/ipfix'
		],
		'cer' => [
			'name' => 'Internet Public Key Infrastructure - Certificate',
			'type' => 'application/pkix-cert'
		],
		'pki' => [
			'name' => 'Internet Public Key Infrastructure - Certificate Management Protocole',
			'type' => 'application/pkixcmp'
		],
		'crl' => [
			'name' => 'Internet Public Key Infrastructure - Certificate Revocation Lists',
			'type' => 'application/pkix-crl'
		],
		'pkipath' => [
			'name' => 'Internet Public Key Infrastructure - Certification Path',
			'type' => 'application/pkix-pkipath'
		],
		'igm' => [
			'name' => 'IOCOM Visimeet',
			'type' => 'application/vnd.insors.igm'
		],
		'rcprofile' => [
			'name' => 'IP Unplugged Roaming Client',
			'type' => 'application/vnd.ipunplugged.rcprofile'
		],
		'irp' => [
			'name' => 'iRepository / Lucidoc Editor',
			'type' => 'application/vnd.irepository.package+xml'
		],
		'jad' => [
			'name' => 'J2ME App Descriptor',
			'type' => 'text/vnd.sun.j2me.app-descriptor'
		],
		'jar' => [
			'name' => 'Java Archive',
			'type' => 'application/java-archive'
		],
		'class' => [
			'name' => 'Java Bytecode File',
			'type' => 'application/java-vm'
		],
		'jnlp' => [
			'name' => 'Java Network Launching Protocol',
			'type' => 'application/x-java-jnlp-file'
		],
		'ser' => [
			'name' => 'Java Serialized Object',
			'type' => 'application/java-serialized-object'
		],
		'java' => [
			'name' => 'Java Source File',
			'type' => '"text/x-java-source,java"'
		],
		'js' => [
			'name' => 'JavaScript',
			'type' => 'application/javascript'
		],
		'json' => [
			'name' => 'JavaScript Object Notation (JSON)',
			'type' => 'application/json'
		],
		'joda' => [
			'name' => 'Joda Archive',
			'type' => 'application/vnd.joost.joda-archive'
		],
		'jpm' => [
			'name' => 'JPEG 2000 Compound Image File Format',
			'type' => 'video/jpm'
		],
		'".jpeg, .jpg"' => [
			'name' => 'JPEG Image',
			'type' => 'image/jpeg'
		],
		'jpgv' => [
			'name' => 'JPGVideo',
			'type' => 'video/jpeg'
		],
		'ktz' => [
			'name' => 'Kahootz',
			'type' => 'application/vnd.kahootz'
		],
		'mmd' => [
			'name' => 'Karaoke on Chipnuts Chipsets',
			'type' => 'application/vnd.chipnuts.karaoke-mmd'
		],
		'karbon' => [
			'name' => 'KDE KOffice Office Suite - Karbon',
			'type' => 'application/vnd.kde.karbon'
		],
		'chrt' => [
			'name' => 'KDE KOffice Office Suite - KChart',
			'type' => 'application/vnd.kde.kchart'
		],
		'kfo' => [
			'name' => 'KDE KOffice Office Suite - Kformula',
			'type' => 'application/vnd.kde.kformula'
		],
		'flw' => [
			'name' => 'KDE KOffice Office Suite - Kivio',
			'type' => 'application/vnd.kde.kivio'
		],
		'kon' => [
			'name' => 'KDE KOffice Office Suite - Kontour',
			'type' => 'application/vnd.kde.kontour'
		],
		'kpr' => [
			'name' => 'KDE KOffice Office Suite - Kpresenter',
			'type' => 'application/vnd.kde.kpresenter'
		],
		'ksp' => [
			'name' => 'KDE KOffice Office Suite - Kspread',
			'type' => 'application/vnd.kde.kspread'
		],
		'kwd' => [
			'name' => 'KDE KOffice Office Suite - Kword',
			'type' => 'application/vnd.kde.kword'
		],
		'htke' => [
			'name' => 'Kenamea App',
			'type' => 'application/vnd.kenameaapp'
		],
		'kia' => [
			'name' => 'Kidspiration',
			'type' => 'application/vnd.kidspiration'
		],
		'kne' => [
			'name' => 'Kinar Applications',
			'type' => 'application/vnd.kinar'
		],
		'sse' => [
			'name' => 'Kodak Storyshare',
			'type' => 'application/vnd.kodak-descriptor'
		],
		'lasxml' => [
			'name' => 'Laser App Enterprise',
			'type' => 'application/vnd.las.las+xml'
		],
		'latex' => [
			'name' => 'LaTeX',
			'type' => 'application/x-latex'
		],
		'lbd' => [
			'name' => 'Life Balance - Desktop Edition',
			'type' => 'application/vnd.llamagraphics.life-balance.desktop'
		],
		'lbe' => [
			'name' => 'Life Balance - Exchange Format',
			'type' => 'application/vnd.llamagraphics.life-balance.exchange+xml'
		],
		'jam' => [
			'name' => 'Lightspeed Audio Lab',
			'type' => 'application/vnd.jam'
		],
		'0.123' => [
			'name' => 'Lotus 1-2-3',
			'type' => 'application/vnd.lotus-1-2-3'
		],
		'apr' => [
			'name' => 'Lotus Approach',
			'type' => 'application/vnd.lotus-approach'
		],
		'pre' => [
			'name' => 'Lotus Freelance',
			'type' => 'application/vnd.lotus-freelance'
		],
		'nsf' => [
			'name' => 'Lotus Notes',
			'type' => 'application/vnd.lotus-notes'
		],
		'org' => [
			'name' => 'Lotus Organizer',
			'type' => 'application/vnd.lotus-organizer'
		],
		'scm' => [
			'name' => 'Lotus Screencam',
			'type' => 'application/vnd.lotus-screencam'
		],
		'lwp' => [
			'name' => 'Lotus Wordpro',
			'type' => 'application/vnd.lotus-wordpro'
		],
		'lvp' => [
			'name' => 'Lucent Voice',
			'type' => 'audio/vnd.lucent.voice'
		],
		'm3u' => [
			'name' => 'M3U (Multimedia Playlist)',
			'type' => 'audio/x-mpegurl'
		],
		'm4v' => [
			'name' => 'M4v',
			'type' => 'video/x-m4v'
		],
		'hqx' => [
			'name' => 'Macintosh BinHex 4.0',
			'type' => 'application/mac-binhex40'
		],
		'portpkg' => [
			'name' => 'MacPorts Port System',
			'type' => 'application/vnd.macports.portpkg'
		],
		'mgp' => [
			'name' => 'MapGuide DBXML',
			'type' => 'application/vnd.osgeo.mapguide.package'
		],
		'mrc' => [
			'name' => 'MARC Formats',
			'type' => 'application/marc'
		],
		'mrcx' => [
			'name' => 'MARC21 XML Schema',
			'type' => 'application/marcxml+xml'
		],
		'mxf' => [
			'name' => 'Material Exchange Format',
			'type' => 'application/mxf'
		],
		'nbp' => [
			'name' => 'Mathematica Notebook Player',
			'type' => 'application/vnd.wolfram.player'
		],
		'ma' => [
			'name' => 'Mathematica Notebooks',
			'type' => 'application/mathematica'
		],
		'mathml' => [
			'name' => 'Mathematical Markup Language',
			'type' => 'application/mathml+xml'
		],
		'mbox' => [
			'name' => 'Mbox database files',
			'type' => 'application/mbox'
		],
		'mc1' => [
			'name' => 'MedCalc',
			'type' => 'application/vnd.medcalcdata'
		],
		'mscml' => [
			'name' => 'Media Server Control Markup Language',
			'type' => 'application/mediaservercontrol+xml'
		],
		'cdkey' => [
			'name' => 'MediaRemote',
			'type' => 'application/vnd.mediastation.cdkey'
		],
		'mwf' => [
			'name' => 'Medical Waveform Encoding Format',
			'type' => 'application/vnd.mfer'
		],
		'mfm' => [
			'name' => 'Melody Format for Mobile Platform',
			'type' => 'application/vnd.mfmp'
		],
		'msh' => [
			'name' => 'Mesh Data Type',
			'type' => 'model/mesh'
		],
		'mads' => [
			'name' => 'Metadata Authority  Description Schema',
			'type' => 'application/mads+xml'
		],
		'mets' => [
			'name' => 'Metadata Encoding and Transmission Standard',
			'type' => 'application/mets+xml'
		],
		'mods' => [
			'name' => 'Metadata Object Description Schema',
			'type' => 'application/mods+xml'
		],
		'meta4' => [
			'name' => 'Metalink',
			'type' => 'application/metalink4+xml'
		],
		'potm' => [
			'name' => 'Micosoft PowerPoint - Macro-Enabled Template File',
			'type' => 'application/vnd.ms-powerpoint.template.macroenabled.12'
		],
		'docm' => [
			'name' => 'Micosoft Word - Macro-Enabled Document',
			'type' => 'application/vnd.ms-word.document.macroenabled.12'
		],
		'dotm' => [
			'name' => 'Micosoft Word - Macro-Enabled Template',
			'type' => 'application/vnd.ms-word.template.macroenabled.12'
		],
		'mcd' => [
			'name' => 'Micro CADAM Helix D&D',
			'type' => 'application/vnd.mcd'
		],
		'flo' => [
			'name' => 'Micrografx',
			'type' => 'application/vnd.micrografx.flo'
		],
		'igx' => [
			'name' => 'Micrografx iGrafx Professional',
			'type' => 'application/vnd.micrografx.igx'
		],
		'es3' => [
			'name' => 'MICROSEC e-Szign¢',
			'type' => 'application/vnd.eszigno3+xml'
		],
		'mdb' => [
			'name' => 'Microsoft Access',
			'type' => 'application/x-msaccess'
		],
		'asf' => [
			'name' => 'Microsoft Advanced Systems Format (ASF)',
			'type' => 'video/x-ms-asf'
		],
		'exe' => [
			'name' => 'Microsoft Application',
			'type' => 'application/x-msdownload'
		],
		'cil' => [
			'name' => 'Microsoft Artgalry',
			'type' => 'application/vnd.ms-artgalry'
		],
		'cab' => [
			'name' => 'Microsoft Cabinet File',
			'type' => 'application/vnd.ms-cab-compressed'
		],
		'ims' => [
			'name' => 'Microsoft Class Server',
			'type' => 'application/vnd.ms-ims'
		],
		'application' => [
			'name' => 'Microsoft ClickOnce',
			'type' => 'application/x-ms-application'
		],
		'clp' => [
			'name' => 'Microsoft Clipboard Clip',
			'type' => 'application/x-msclip'
		],
		'mdi' => [
			'name' => 'Microsoft Document Imaging Format',
			'type' => 'image/vnd.ms-modi'
		],
		'eot' => [
			'name' => 'Microsoft Embedded OpenType',
			'type' => 'application/vnd.ms-fontobject'
		],
		'xls' => [
			'name' => 'Microsoft Excel',
			'type' => 'application/vnd.ms-excel'
		],
		'xlam' => [
			'name' => 'Microsoft Excel - Add-In File',
			'type' => 'application/vnd.ms-excel.addin.macroenabled.12'
		],
		'xlsb' => [
			'name' => 'Microsoft Excel - Binary Workbook',
			'type' => 'application/vnd.ms-excel.sheet.binary.macroenabled.12'
		],
		'xltm' => [
			'name' => 'Microsoft Excel - Macro-Enabled Template File',
			'type' => 'application/vnd.ms-excel.template.macroenabled.12'
		],
		'xlsm' => [
			'name' => 'Microsoft Excel - Macro-Enabled Workbook',
			'type' => 'application/vnd.ms-excel.sheet.macroenabled.12'
		],
		'chm' => [
			'name' => 'Microsoft Html Help File',
			'type' => 'application/vnd.ms-htmlhelp'
		],
		'crd' => [
			'name' => 'Microsoft Information Card',
			'type' => 'application/x-mscardfile'
		],
		'lrm' => [
			'name' => 'Microsoft Learning Resource Module',
			'type' => 'application/vnd.ms-lrm'
		],
		'mvb' => [
			'name' => 'Microsoft MediaView',
			'type' => 'application/x-msmediaview'
		],
		'mny' => [
			'name' => 'Microsoft Money',
			'type' => 'application/x-msmoney'
		],
		'pptx' => [
			'name' => 'Microsoft Office - OOXML - Presentation',
			'type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
		],
		'sldx' => [
			'name' => 'Microsoft Office - OOXML - Presentation (Slide)',
			'type' => 'application/vnd.openxmlformats-officedocument.presentationml.slide'
		],
		'ppsx' => [
			'name' => 'Microsoft Office - OOXML - Presentation (Slideshow)',
			'type' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow'
		],
		'potx' => [
			'name' => 'Microsoft Office - OOXML - Presentation Template',
			'type' => 'application/vnd.openxmlformats-officedocument.presentationml.template'
		],
		'xlsx' => [
			'name' => 'Microsoft Office - OOXML - Spreadsheet',
			'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		],
		'xltx' => [
			'name' => 'Microsoft Office - OOXML - Spreadsheet Teplate',
			'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
		],
		'docx' => [
			'name' => 'Microsoft Office - OOXML - Word Document',
			'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		],
		'dotx' => [
			'name' => 'Microsoft Office - OOXML - Word Document Template',
			'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template'
		],
		'obd' => [
			'name' => 'Microsoft Office Binder',
			'type' => 'application/x-msbinder'
		],
		'thmx' => [
			'name' => 'Microsoft Office System Release Theme',
			'type' => 'application/vnd.ms-officetheme'
		],
		'onetoc' => [
			'name' => 'Microsoft OneNote',
			'type' => 'application/onenote'
		],
		'pya' => [
			'name' => 'Microsoft PlayReady Ecosystem',
			'type' => 'audio/vnd.ms-playready.media.pya'
		],
		'pyv' => [
			'name' => 'Microsoft PlayReady Ecosystem Video',
			'type' => 'video/vnd.ms-playready.media.pyv'
		],
		'ppt' => [
			'name' => 'Microsoft PowerPoint',
			'type' => 'application/vnd.ms-powerpoint'
		],
		'ppam' => [
			'name' => 'Microsoft PowerPoint - Add-in file',
			'type' => 'application/vnd.ms-powerpoint.addin.macroenabled.12'
		],
		'sldm' => [
			'name' => 'Microsoft PowerPoint - Macro-Enabled Open XML Slide',
			'type' => 'application/vnd.ms-powerpoint.slide.macroenabled.12'
		],
		'pptm' => [
			'name' => 'Microsoft PowerPoint - Macro-Enabled Presentation File',
			'type' => 'application/vnd.ms-powerpoint.presentation.macroenabled.12'
		],
		'ppsm' => [
			'name' => 'Microsoft PowerPoint - Macro-Enabled Slide Show File',
			'type' => 'application/vnd.ms-powerpoint.slideshow.macroenabled.12'
		],
		'mpp' => [
			'name' => 'Microsoft Project',
			'type' => 'application/vnd.ms-project'
		],
		'pub' => [
			'name' => 'Microsoft Publisher',
			'type' => 'application/x-mspublisher'
		],
		'scd' => [
			'name' => 'Microsoft Schedule+',
			'type' => 'application/x-msschedule'
		],
		'xap' => [
			'name' => 'Microsoft Silverlight',
			'type' => 'application/x-silverlight-app'
		],
		'stl' => [
			'name' => 'Microsoft Trust UI Provider - Certificate Trust Link',
			'type' => 'application/vnd.ms-pki.stl'
		],
		'cat' => [
			'name' => 'Microsoft Trust UI Provider - Security Catalog',
			'type' => 'application/vnd.ms-pki.seccat'
		],
		'vsd' => [
			'name' => 'Microsoft Visio',
			'type' => 'application/vnd.visio'
		],
		'wm' => [
			'name' => 'Microsoft Windows Media',
			'type' => 'video/x-ms-wm'
		],
		'wma' => [
			'name' => 'Microsoft Windows Media Audio',
			'type' => 'audio/x-ms-wma'
		],
		'wax' => [
			'name' => 'Microsoft Windows Media Audio Redirector',
			'type' => 'audio/x-ms-wax'
		],
		'wmx' => [
			'name' => 'Microsoft Windows Media Audio/Video Playlist',
			'type' => 'video/x-ms-wmx'
		],
		'wmd' => [
			'name' => 'Microsoft Windows Media Player Download Package',
			'type' => 'application/x-ms-wmd'
		],
		'wpl' => [
			'name' => 'Microsoft Windows Media Player Playlist',
			'type' => 'application/vnd.ms-wpl'
		],
		'wmz' => [
			'name' => 'Microsoft Windows Media Player Skin Package',
			'type' => 'application/x-ms-wmz'
		],
		'wmv' => [
			'name' => 'Microsoft Windows Media Video',
			'type' => 'video/x-ms-wmv'
		],
		'wvx' => [
			'name' => 'Microsoft Windows Media Video Playlist',
			'type' => 'video/x-ms-wvx'
		],
		'wmf' => [
			'name' => 'Microsoft Windows Metafile',
			'type' => 'application/x-msmetafile'
		],
		'trm' => [
			'name' => 'Microsoft Windows Terminal Services',
			'type' => 'application/x-msterminal'
		],
		'doc' => [
			'name' => 'Microsoft Word',
			'type' => 'application/msword'
		],
		'wri' => [
			'name' => 'Microsoft Wordpad',
			'type' => 'application/x-mswrite'
		],
		'wps' => [
			'name' => 'Microsoft Works',
			'type' => 'application/vnd.ms-works'
		],
		'xbap' => [
			'name' => 'Microsoft XAML Browser Application',
			'type' => 'application/x-ms-xbap'
		],
		'xps' => [
			'name' => 'Microsoft XML Paper Specification',
			'type' => 'application/vnd.ms-xpsdocument'
		],
		'mid' => [
			'name' => 'MIDI - Musical Instrument Digital Interface',
			'type' => 'audio/midi'
		],
		'mpy' => [
			'name' => 'MiniPay',
			'type' => 'application/vnd.ibm.minipay'
		],
		'afp' => [
			'name' => 'MO:DCA-P',
			'type' => 'application/vnd.ibm.modcap'
		],
		'rms' => [
			'name' => 'Mobile Information Device Profile',
			'type' => 'application/vnd.jcp.javame.midlet-rms'
		],
		'tmo' => [
			'name' => 'MobileTV',
			'type' => 'application/vnd.tmobile-livetv'
		],
		'prc' => [
			'name' => 'Mobipocket',
			'type' => 'application/x-mobipocket-ebook'
		],
		'mbk' => [
			'name' => 'Mobius Management Systems - Basket file',
			'type' => 'application/vnd.mobius.mbk'
		],
		'dis' => [
			'name' => 'Mobius Management Systems - Distribution Database',
			'type' => 'application/vnd.mobius.dis'
		],
		'plc' => [
			'name' => 'Mobius Management Systems - Policy Definition Language File',
			'type' => 'application/vnd.mobius.plc'
		],
		'mqy' => [
			'name' => 'Mobius Management Systems - Query File',
			'type' => 'application/vnd.mobius.mqy'
		],
		'msl' => [
			'name' => 'Mobius Management Systems - Script Language',
			'type' => 'application/vnd.mobius.msl'
		],
		'txf' => [
			'name' => 'Mobius Management Systems - Topic Index File',
			'type' => 'application/vnd.mobius.txf'
		],
		'daf' => [
			'name' => 'Mobius Management Systems - UniversalArchive',
			'type' => 'application/vnd.mobius.daf'
		],
		'fly' => [
			'name' => 'mod_fly / fly.cgi',
			'type' => 'text/vnd.fly'
		],
		'mpc' => [
			'name' => 'Mophun Certificate',
			'type' => 'application/vnd.mophun.certificate'
		],
		'mpn' => [
			'name' => 'Mophun VM',
			'type' => 'application/vnd.mophun.application'
		],
		'mj2' => [
			'name' => 'Motion JPEG 2000',
			'type' => 'video/mj2'
		],
		'mpga' => [
			'name' => 'MPEG Audio',
			'type' => 'audio/mpeg'
		],
		'mxu' => [
			'name' => 'MPEG Url',
			'type' => 'video/vnd.mpegurl'
		],
		'mpeg' => [
			'name' => 'MPEG Video',
			'type' => 'video/mpeg'
		],
		'm21' => [
			'name' => 'MPEG-21',
			'type' => 'application/mp21'
		],
		'mp4a' => [
			'name' => 'MPEG-4 Audio',
			'type' => 'audio/mp4'
		],
		'mp4' => [
			'name' => 'MPEG-4 Video',
			'type' => 'video/mp4'
		],
		'mp4' => [
			'name' => 'MPEG4',
			'type' => 'application/mp4'
		],
		'm3u8' => [
			'name' => 'Multimedia Playlist Unicode',
			'type' => 'application/vnd.apple.mpegurl'
		],
		'mus' => [
			'name' => 'MUsical Score Interpreted Code Invented  for the ASCII designation of Notation',
			'type' => 'application/vnd.musician'
		],
		'msty' => [
			'name' => 'Muvee Automatic Video Editing',
			'type' => 'application/vnd.muvee.style'
		],
		'mxml' => [
			'name' => 'MXML',
			'type' => 'application/xv+xml'
		],
		'ngdat' => [
			'name' => 'N-Gage Game Data',
			'type' => 'application/vnd.nokia.n-gage.data'
		],
		'n-gage' => [
			'name' => 'N-Gage Game Installer',
			'type' => 'application/vnd.nokia.n-gage.symbian.install'
		],
		'ncx' => [
			'name' => 'Navigation Control file for XML (for ePub)',
			'type' => 'application/x-dtbncx+xml'
		],
		'nc' => [
			'name' => 'Network Common Data Form (NetCDF)',
			'type' => 'application/x-netcdf'
		],
		'nlu' => [
			'name' => 'neuroLanguage',
			'type' => 'application/vnd.neurolanguage.nlu'
		],
		'dna' => [
			'name' => 'New Moon Liftoff/DNA',
			'type' => 'application/vnd.dna'
		],
		'nnd' => [
			'name' => 'NobleNet Directory',
			'type' => 'application/vnd.noblenet-directory'
		],
		'nns' => [
			'name' => 'NobleNet Sealer',
			'type' => 'application/vnd.noblenet-sealer'
		],
		'nnw' => [
			'name' => 'NobleNet Web',
			'type' => 'application/vnd.noblenet-web'
		],
		'rpst' => [
			'name' => 'Nokia Radio Application - Preset',
			'type' => 'application/vnd.nokia.radio-preset'
		],
		'rpss' => [
			'name' => 'Nokia Radio Application - Preset',
			'type' => 'application/vnd.nokia.radio-presets'
		],
		'n3' => [
			'name' => 'Notation3',
			'type' => 'text/n3'
		],
		'edm' => [
			'name' => 'Novadigm\'s RADIA and EDM products',
			'type' => 'application/vnd.novadigm.edm'
		],
		'edx' => [
			'name' => 'Novadigm\'s RADIA and EDM products',
			'type' => 'application/vnd.novadigm.edx'
		],
		'ext' => [
			'name' => 'Novadigm\'s RADIA and EDM products',
			'type' => 'application/vnd.novadigm.ext'
		],
		'gph' => [
			'name' => 'NpGraphIt',
			'type' => 'application/vnd.flographit'
		],
		'ecelp4800' => [
			'name' => 'Nuera ECELP 4800',
			'type' => 'audio/vnd.nuera.ecelp4800'
		],
		'ecelp7470' => [
			'name' => 'Nuera ECELP 7470',
			'type' => 'audio/vnd.nuera.ecelp7470'
		],
		'ecelp9600' => [
			'name' => 'Nuera ECELP 9600',
			'type' => 'audio/vnd.nuera.ecelp9600'
		],
		'oda' => [
			'name' => 'Office Document Architecture',
			'type' => 'application/oda'
		],
		'ogx' => [
			'name' => 'Ogg',
			'type' => 'application/ogg'
		],
		'oga' => [
			'name' => 'Ogg Audio',
			'type' => 'audio/ogg'
		],
		'ogv' => [
			'name' => 'Ogg Video',
			'type' => 'video/ogg'
		],
		'dd2' => [
			'name' => 'OMA Download Agents',
			'type' => 'application/vnd.oma.dd2+xml'
		],
		'oth' => [
			'name' => 'Open Document Text Web',
			'type' => 'application/vnd.oasis.opendocument.text-web'
		],
		'opf' => [
			'name' => 'Open eBook Publication Structure',
			'type' => 'application/oebps-package+xml'
		],
		'qbo' => [
			'name' => 'Open Financial Exchange',
			'type' => 'application/vnd.intu.qbo'
		],
		'oxt' => [
			'name' => 'Open Office Extension',
			'type' => 'application/vnd.openofficeorg.extension'
		],
		'osf' => [
			'name' => 'Open Score Format',
			'type' => 'application/vnd.yamaha.openscoreformat'
		],
		'weba' => [
			'name' => 'Open Web Media Project - Audio',
			'type' => 'audio/webm'
		],
		'webm' => [
			'name' => 'Open Web Media Project - Video',
			'type' => 'video/webm'
		],
		'odc' => [
			'name' => 'OpenDocument Chart',
			'type' => 'application/vnd.oasis.opendocument.chart'
		],
		'otc' => [
			'name' => 'OpenDocument Chart Template',
			'type' => 'application/vnd.oasis.opendocument.chart-template'
		],
		'odb' => [
			'name' => 'OpenDocument Database',
			'type' => 'application/vnd.oasis.opendocument.database'
		],
		'odf' => [
			'name' => 'OpenDocument Formula',
			'type' => 'application/vnd.oasis.opendocument.formula'
		],
		'odft' => [
			'name' => 'OpenDocument Formula Template',
			'type' => 'application/vnd.oasis.opendocument.formula-template'
		],
		'odg' => [
			'name' => 'OpenDocument Graphics',
			'type' => 'application/vnd.oasis.opendocument.graphics'
		],
		'otg' => [
			'name' => 'OpenDocument Graphics Template',
			'type' => 'application/vnd.oasis.opendocument.graphics-template'
		],
		'odi' => [
			'name' => 'OpenDocument Image',
			'type' => 'application/vnd.oasis.opendocument.image'
		],
		'oti' => [
			'name' => 'OpenDocument Image Template',
			'type' => 'application/vnd.oasis.opendocument.image-template'
		],
		'odp' => [
			'name' => 'OpenDocument Presentation',
			'type' => 'application/vnd.oasis.opendocument.presentation'
		],
		'otp' => [
			'name' => 'OpenDocument Presentation Template',
			'type' => 'application/vnd.oasis.opendocument.presentation-template'
		],
		'ods' => [
			'name' => 'OpenDocument Spreadsheet',
			'type' => 'application/vnd.oasis.opendocument.spreadsheet'
		],
		'ots' => [
			'name' => 'OpenDocument Spreadsheet Template',
			'type' => 'application/vnd.oasis.opendocument.spreadsheet-template'
		],
		'odt' => [
			'name' => 'OpenDocument Text',
			'type' => 'application/vnd.oasis.opendocument.text'
		],
		'odm' => [
			'name' => 'OpenDocument Text Master',
			'type' => 'application/vnd.oasis.opendocument.text-master'
		],
		'ott' => [
			'name' => 'OpenDocument Text Template',
			'type' => 'application/vnd.oasis.opendocument.text-template'
		],
		'ktx' => [
			'name' => 'OpenGL Textures (KTX)',
			'type' => 'image/ktx'
		],
		'sxc' => [
			'name' => 'OpenOffice - Calc (Spreadsheet)',
			'type' => 'application/vnd.sun.xml.calc'
		],
		'stc' => [
			'name' => 'OpenOffice - Calc Template (Spreadsheet)',
			'type' => 'application/vnd.sun.xml.calc.template'
		],
		'sxd' => [
			'name' => 'OpenOffice - Draw (Graphics)',
			'type' => 'application/vnd.sun.xml.draw'
		],
		'std' => [
			'name' => 'OpenOffice - Draw Template (Graphics)',
			'type' => 'application/vnd.sun.xml.draw.template'
		],
		'sxi' => [
			'name' => 'OpenOffice - Impress (Presentation)',
			'type' => 'application/vnd.sun.xml.impress'
		],
		'sti' => [
			'name' => 'OpenOffice - Impress Template (Presentation)',
			'type' => 'application/vnd.sun.xml.impress.template'
		],
		'sxm' => [
			'name' => 'OpenOffice - Math (Formula)',
			'type' => 'application/vnd.sun.xml.math'
		],
		'sxw' => [
			'name' => 'OpenOffice - Writer (Text - HTML)',
			'type' => 'application/vnd.sun.xml.writer'
		],
		'sxg' => [
			'name' => 'OpenOffice - Writer (Text - HTML)',
			'type' => 'application/vnd.sun.xml.writer.global'
		],
		'stw' => [
			'name' => 'OpenOffice - Writer Template (Text - HTML)',
			'type' => 'application/vnd.sun.xml.writer.template'
		],
		'otf' => [
			'name' => 'OpenType Font File',
			'type' => 'application/x-font-otf'
		],
		'osfpvg' => [
			'name' => 'OSFPVG',
			'type' => 'application/vnd.yamaha.openscoreformat.osfpvg+xml'
		],
		'dp' => [
			'name' => 'OSGi Deployment Package',
			'type' => 'application/vnd.osgi.dp'
		],
		'pdb' => [
			'name' => 'PalmOS Data',
			'type' => 'application/vnd.palm'
		],
		'p' => [
			'name' => 'Pascal Source File',
			'type' => 'text/x-pascal'
		],
		'paw' => [
			'name' => 'PawaaFILE',
			'type' => 'application/vnd.pawaafile'
		],
		'pclxl' => [
			'name' => 'PCL 6 Enhanced (Formely PCL XL)',
			'type' => 'application/vnd.hp-pclxl'
		],
		'efif' => [
			'name' => 'Pcsel eFIF File',
			'type' => 'application/vnd.picsel'
		],
		'pcx' => [
			'name' => 'PCX Image',
			'type' => 'image/x-pcx'
		],
		'psd' => [
			'name' => 'Photoshop Document',
			'type' => 'image/vnd.adobe.photoshop'
		],
		'prf' => [
			'name' => 'PICSRules',
			'type' => 'application/pics-rules'
		],
		'pic' => [
			'name' => 'PICT Image',
			'type' => 'image/x-pict'
		],
		'chat' => [
			'name' => 'pIRCh',
			'type' => 'application/x-chat'
		],
		'p10' => [
			'name' => 'PKCS #10 - Certification Request Standard',
			'type' => 'application/pkcs10'
		],
		'p12' => [
			'name' => 'PKCS #12 - Personal Information Exchange Syntax Standard',
			'type' => 'application/x-pkcs12'
		],
		'p7m' => [
			'name' => 'PKCS #7 - Cryptographic Message Syntax Standard',
			'type' => 'application/pkcs7-mime'
		],
		'p7s' => [
			'name' => 'PKCS #7 - Cryptographic Message Syntax Standard',
			'type' => 'application/pkcs7-signature'
		],
		'p7r' => [
			'name' => 'PKCS #7 - Cryptographic Message Syntax Standard (Certificate Request Response)',
			'type' => 'application/x-pkcs7-certreqresp'
		],
		'p7b' => [
			'name' => 'PKCS #7 - Cryptographic Message Syntax Standard (Certificates)',
			'type' => 'application/x-pkcs7-certificates'
		],
		'p8' => [
			'name' => 'PKCS #8 - Private-Key Information Syntax Standard',
			'type' => 'application/pkcs8'
		],
		'plf' => [
			'name' => 'PocketLearn Viewers',
			'type' => 'application/vnd.pocketlearn'
		],
		'pnm' => [
			'name' => 'Portable Anymap Image',
			'type' => 'image/x-portable-anymap'
		],
		'pbm' => [
			'name' => 'Portable Bitmap Format',
			'type' => 'image/x-portable-bitmap'
		],
		'pcf' => [
			'name' => 'Portable Compiled Format',
			'type' => 'application/x-font-pcf'
		],
		'pfr' => [
			'name' => 'Portable Font Resource',
			'type' => 'application/font-tdpfr'
		],
		'pgn' => [
			'name' => 'Portable Game Notation (Chess Games)',
			'type' => 'application/x-chess-pgn'
		],
		'pgm' => [
			'name' => 'Portable Graymap Format',
			'type' => 'image/x-portable-graymap'
		],
		'png' => [
			'name' => 'Portable Network Graphics (PNG)',
			'type' => 'image/png'
		],
		'ppm' => [
			'name' => 'Portable Pixmap Format',
			'type' => 'image/x-portable-pixmap'
		],
		'pskcxml' => [
			'name' => 'Portable Symmetric Key Container',
			'type' => 'application/pskc+xml'
		],
		'pml' => [
			'name' => 'PosML',
			'type' => 'application/vnd.ctc-posml'
		],
		'ai' => [
			'name' => 'PostScript',
			'type' => 'application/postscript'
		],
		'pfa' => [
			'name' => 'PostScript Fonts',
			'type' => 'application/x-font-type1'
		],
		'pbd' => [
			'name' => 'PowerBuilder',
			'type' => 'application/vnd.powerbuilder6'
		],
		'' => [
			'name' => 'Pretty Good Privacy',
			'type' => 'application/pgp-encrypted'
		],
		'pgp' => [
			'name' => 'Pretty Good Privacy - Signature',
			'type' => 'application/pgp-signature'
		],
		'box' => [
			'name' => 'Preview Systems ZipLock/VBox',
			'type' => 'application/vnd.previewsystems.box'
		],
		'ptid' => [
			'name' => 'Princeton Video Image',
			'type' => 'application/vnd.pvi.ptid1'
		],
		'pls' => [
			'name' => 'Pronunciation Lexicon Specification',
			'type' => 'application/pls+xml'
		],
		'str' => [
			'name' => 'Proprietary P&G Standard Reporting System',
			'type' => 'application/vnd.pg.format'
		],
		'ei6' => [
			'name' => 'Proprietary P&G Standard Reporting System',
			'type' => 'application/vnd.pg.osasli'
		],
		'dsc' => [
			'name' => 'PRS Lines Tag',
			'type' => 'text/prs.lines.tag'
		],
		'psf' => [
			'name' => 'PSF Fonts',
			'type' => 'application/x-font-linux-psf'
		],
		'qps' => [
			'name' => 'PubliShare Objects',
			'type' => 'application/vnd.publishare-delta-tree'
		],
		'wg' => [
			'name' => 'Qualcomm\'s Plaza Mobile Internet',
			'type' => 'application/vnd.pmi.widget'
		],
		'qxd' => [
			'name' => 'QuarkXpress',
			'type' => 'application/vnd.quark.quarkxpress'
		],
		'esf' => [
			'name' => 'QUASS Stream Player',
			'type' => 'application/vnd.epson.esf'
		],
		'msf' => [
			'name' => 'QUASS Stream Player',
			'type' => 'application/vnd.epson.msf'
		],
		'ssf' => [
			'name' => 'QUASS Stream Player',
			'type' => 'application/vnd.epson.ssf'
		],
		'qam' => [
			'name' => 'QuickAnime Player',
			'type' => 'application/vnd.epson.quickanime'
		],
		'qfx' => [
			'name' => 'Quicken',
			'type' => 'application/vnd.intu.qfx'
		],
		'qt' => [
			'name' => 'Quicktime Video',
			'type' => 'video/quicktime'
		],
		'rar' => [
			'name' => 'RAR Archive',
			'type' => 'application/x-rar-compressed'
		],
		'ram' => [
			'name' => 'Real Audio Sound',
			'type' => 'audio/x-pn-realaudio'
		],
		'rmp' => [
			'name' => 'Real Audio Sound',
			'type' => 'audio/x-pn-realaudio-plugin'
		],
		'rsd' => [
			'name' => 'Really Simple Discovery',
			'type' => 'application/rsd+xml'
		],
		'rm' => [
			'name' => 'RealMedia',
			'type' => 'application/vnd.rn-realmedia'
		],
		'bed' => [
			'name' => 'RealVNC',
			'type' => 'application/vnd.realvnc.bed'
		],
		'mxl' => [
			'name' => 'Recordare Applications',
			'type' => 'application/vnd.recordare.musicxml'
		],
		'musicxml' => [
			'name' => 'Recordare Applications',
			'type' => 'application/vnd.recordare.musicxml+xml'
		],
		'rnc' => [
			'name' => 'Relax NG Compact Syntax',
			'type' => 'application/relax-ng-compact-syntax'
		],
		'rdz' => [
			'name' => 'RemoteDocs R-Viewer',
			'type' => 'application/vnd.data-vision.rdz'
		],
		'rdf' => [
			'name' => 'Resource Description Framework',
			'type' => 'application/rdf+xml'
		],
		'rp9' => [
			'name' => 'RetroPlatform Player',
			'type' => 'application/vnd.cloanto.rp9'
		],
		'jisp' => [
			'name' => 'RhymBox',
			'type' => 'application/vnd.jisp'
		],
		'rtf' => [
			'name' => 'Rich Text Format',
			'type' => 'application/rtf'
		],
		'rtx' => [
			'name' => 'Rich Text Format (RTF)',
			'type' => 'text/richtext'
		],
		'link66' => [
			'name' => 'ROUTE 66 Location Based Services',
			'type' => 'application/vnd.route66.link66+xml'
		],
		'".rss, .xml"' => [
			'name' => 'RSS - Really Simple Syndication',
			'type' => 'application/rss+xml'
		],
		'shf' => [
			'name' => 'S Hexdump Format',
			'type' => 'application/shf+xml'
		],
		'st' => [
			'name' => 'SailingTracker',
			'type' => 'application/vnd.sailingtracker.track'
		],
		'svg' => [
			'name' => 'Scalable Vector Graphics (SVG)',
			'type' => 'image/svg+xml'
		],
		'sus' => [
			'name' => 'ScheduleUs',
			'type' => 'application/vnd.sus-calendar'
		],
		'sru' => [
			'name' => 'Search/Retrieve via URL Response Format',
			'type' => 'application/sru+xml'
		],
		'setpay' => [
			'name' => 'Secure Electronic Transaction - Payment',
			'type' => 'application/set-payment-initiation'
		],
		'setreg' => [
			'name' => 'Secure Electronic Transaction - Registration',
			'type' => 'application/set-registration-initiation'
		],
		'sema' => [
			'name' => 'Secured eMail',
			'type' => 'application/vnd.sema'
		],
		'semd' => [
			'name' => 'Secured eMail',
			'type' => 'application/vnd.semd'
		],
		'semf' => [
			'name' => 'Secured eMail',
			'type' => 'application/vnd.semf'
		],
		'see' => [
			'name' => 'SeeMail',
			'type' => 'application/vnd.seemail'
		],
		'snf' => [
			'name' => 'Server Normal Format',
			'type' => 'application/x-font-snf'
		],
		'spq' => [
			'name' => 'Server-Based Certificate Validation Protocol - Validation Policies - Request',
			'type' => 'application/scvp-vp-request'
		],
		'spp' => [
			'name' => 'Server-Based Certificate Validation Protocol - Validation Policies - Response',
			'type' => 'application/scvp-vp-response'
		],
		'scq' => [
			'name' => 'Server-Based Certificate Validation Protocol - Validation Request',
			'type' => 'application/scvp-cv-request'
		],
		'scs' => [
			'name' => 'Server-Based Certificate Validation Protocol - Validation Response',
			'type' => 'application/scvp-cv-response'
		],
		'sdp' => [
			'name' => 'Session Description Protocol',
			'type' => 'application/sdp'
		],
		'etx' => [
			'name' => 'Setext',
			'type' => 'text/x-setext'
		],
		'movie' => [
			'name' => 'SGI Movie',
			'type' => 'video/x-sgi-movie'
		],
		'ifm' => [
			'name' => 'Shana Informed Filler',
			'type' => 'application/vnd.shana.informed.formdata'
		],
		'itp' => [
			'name' => 'Shana Informed Filler',
			'type' => 'application/vnd.shana.informed.formtemplate'
		],
		'iif' => [
			'name' => 'Shana Informed Filler',
			'type' => 'application/vnd.shana.informed.interchange'
		],
		'ipk' => [
			'name' => 'Shana Informed Filler',
			'type' => 'application/vnd.shana.informed.package'
		],
		'tfi' => [
			'name' => 'Sharing Transaction Fraud Data',
			'type' => 'application/thraud+xml'
		],
		'shar' => [
			'name' => 'Shell Archive',
			'type' => 'application/x-shar'
		],
		'rgb' => [
			'name' => 'Silicon Graphics RGB Bitmap',
			'type' => 'image/x-rgb'
		],
		'slt' => [
			'name' => 'SimpleAnimeLite Player',
			'type' => 'application/vnd.epson.salt'
		],
		'aso' => [
			'name' => 'Simply Accounting',
			'type' => 'application/vnd.accpac.simply.aso'
		],
		'imp' => [
			'name' => 'Simply Accounting - Data Import',
			'type' => 'application/vnd.accpac.simply.imp'
		],
		'twd' => [
			'name' => 'SimTech MindMapper',
			'type' => 'application/vnd.simtech-mindmapper'
		],
		'csp' => [
			'name' => 'Sixth Floor Media - CommonSpace',
			'type' => 'application/vnd.commonspace'
		],
		'saf' => [
			'name' => 'SMAF Audio',
			'type' => 'application/vnd.yamaha.smaf-audio'
		],
		'mmf' => [
			'name' => 'SMAF File',
			'type' => 'application/vnd.smaf'
		],
		'spf' => [
			'name' => 'SMAF Phrase',
			'type' => 'application/vnd.yamaha.smaf-phrase'
		],
		'teacher' => [
			'name' => 'SMART Technologies Apps',
			'type' => 'application/vnd.smart.teacher'
		],
		'svd' => [
			'name' => 'SourceView Document',
			'type' => 'application/vnd.svd'
		],
		'rq' => [
			'name' => 'SPARQL - Query',
			'type' => 'application/sparql-query'
		],
		'srx' => [
			'name' => 'SPARQL - Results',
			'type' => 'application/sparql-results+xml'
		],
		'gram' => [
			'name' => 'Speech Recognition Grammar Specification',
			'type' => 'application/srgs'
		],
		'grxml' => [
			'name' => 'Speech Recognition Grammar Specification - XML',
			'type' => 'application/srgs+xml'
		],
		'ssml' => [
			'name' => 'Speech Synthesis Markup Language',
			'type' => 'application/ssml+xml'
		],
		'skp' => [
			'name' => 'SSEYO Koan Play File',
			'type' => 'application/vnd.koan'
		],
		'sgml' => [
			'name' => 'Standard Generalized Markup Language (SGML)',
			'type' => 'text/sgml'
		],
		'sdc' => [
			'name' => 'StarOffice - Calc',
			'type' => 'application/vnd.stardivision.calc'
		],
		'sda' => [
			'name' => 'StarOffice - Draw',
			'type' => 'application/vnd.stardivision.draw'
		],
		'sdd' => [
			'name' => 'StarOffice - Impress',
			'type' => 'application/vnd.stardivision.impress'
		],
		'smf' => [
			'name' => 'StarOffice - Math',
			'type' => 'application/vnd.stardivision.math'
		],
		'sdw' => [
			'name' => 'StarOffice - Writer',
			'type' => 'application/vnd.stardivision.writer'
		],
		'sgl' => [
			'name' => 'StarOffice - Writer  (Global)',
			'type' => 'application/vnd.stardivision.writer-global'
		],
		'sm' => [
			'name' => 'StepMania',
			'type' => 'application/vnd.stepmania.stepchart'
		],
		'sit' => [
			'name' => 'Stuffit Archive',
			'type' => 'application/x-stuffit'
		],
		'sitx' => [
			'name' => 'Stuffit Archive',
			'type' => 'application/x-stuffitx'
		],
		'sdkm' => [
			'name' => 'SudokuMagic',
			'type' => 'application/vnd.solent.sdkm+xml'
		],
		'xo' => [
			'name' => 'Sugar Linux Application Bundle',
			'type' => 'application/vnd.olpc-sugar'
		],
		'au' => [
			'name' => 'Sun Audio - Au file format',
			'type' => 'audio/basic'
		],
		'wqd' => [
			'name' => 'SundaHus WQ',
			'type' => 'application/vnd.wqd'
		],
		'sis' => [
			'name' => 'Symbian Install Package',
			'type' => 'application/vnd.symbian.install'
		],
		'smi' => [
			'name' => 'Synchronized Multimedia Integration Language',
			'type' => 'application/smil+xml'
		],
		'xsm' => [
			'name' => 'SyncML',
			'type' => 'application/vnd.syncml+xml'
		],
		'bdm' => [
			'name' => 'SyncML - Device Management',
			'type' => 'application/vnd.syncml.dm+wbxml'
		],
		'xdm' => [
			'name' => 'SyncML - Device Management',
			'type' => 'application/vnd.syncml.dm+xml'
		],
		'sv4cpio' => [
			'name' => 'System V Release 4 CPIO Archive',
			'type' => 'application/x-sv4cpio'
		],
		'sv4crc' => [
			'name' => 'System V Release 4 CPIO Checksum Data',
			'type' => 'application/x-sv4crc'
		],
		'sbml' => [
			'name' => 'Systems Biology Markup Language',
			'type' => 'application/sbml+xml'
		],
		'tsv' => [
			'name' => 'Tab Seperated Values',
			'type' => 'text/tab-separated-values'
		],
		'tiff' => [
			'name' => 'Tagged Image File Format',
			'type' => 'image/tiff'
		],
		'tao' => [
			'name' => 'Tao Intent',
			'type' => 'application/vnd.tao.intent-module-archive'
		],
		'tar' => [
			'name' => 'Tar File (Tape Archive)',
			'type' => 'application/x-tar'
		],
		'tcl' => [
			'name' => 'Tcl Script',
			'type' => 'application/x-tcl'
		],
		'tex' => [
			'name' => 'TeX',
			'type' => 'application/x-tex'
		],
		'tfm' => [
			'name' => 'TeX Font Metric',
			'type' => 'application/x-tex-tfm'
		],
		'tei' => [
			'name' => 'Text Encoding and Interchange',
			'type' => 'application/tei+xml'
		],
		'txt' => [
			'name' => 'Text File',
			'type' => 'text/plain'
		],
		'dxp' => [
			'name' => 'TIBCO Spotfire',
			'type' => 'application/vnd.spotfire.dxp'
		],
		'sfs' => [
			'name' => 'TIBCO Spotfire',
			'type' => 'application/vnd.spotfire.sfs'
		],
		'tsd' => [
			'name' => 'Time Stamped Data Envelope',
			'type' => 'application/timestamped-data'
		],
		'tpt' => [
			'name' => 'TRI Systems Config',
			'type' => 'application/vnd.trid.tpt'
		],
		'mxs' => [
			'name' => 'Triscape Map Explorer',
			'type' => 'application/vnd.triscape.mxs'
		],
		't' => [
			'name' => 'troff',
			'type' => 'text/troff'
		],
		'tra' => [
			'name' => 'True BASIC',
			'type' => 'application/vnd.trueapp'
		],
		'ttf' => [
			'name' => 'TrueType Font',
			'type' => 'application/x-font-ttf'
		],
		'ttl' => [
			'name' => 'Turtle (Terse RDF Triple Language)',
			'type' => 'text/turtle'
		],
		'umj' => [
			'name' => 'UMAJIN',
			'type' => 'application/vnd.umajin'
		],
		'uoml' => [
			'name' => 'Unique Object Markup Language',
			'type' => 'application/vnd.uoml+xml'
		],
		'unityweb' => [
			'name' => 'Unity 3d',
			'type' => 'application/vnd.unity'
		],
		'ufd' => [
			'name' => 'Universal Forms Description Language',
			'type' => 'application/vnd.ufdl'
		],
		'uri' => [
			'name' => 'URI Resolution Services',
			'type' => 'text/uri-list'
		],
		'utz' => [
			'name' => 'User Interface Quartz - Theme (Symbian)',
			'type' => 'application/vnd.uiq.theme'
		],
		'ustar' => [
			'name' => 'Ustar (Uniform Standard Tape Archive)',
			'type' => 'application/x-ustar'
		],
		'uu' => [
			'name' => 'UUEncode',
			'type' => 'text/x-uuencode'
		],
		'vcs' => [
			'name' => 'vCalendar',
			'type' => 'text/x-vcalendar'
		],
		'vcf' => [
			'name' => 'vCard',
			'type' => 'text/x-vcard'
		],
		'vcd' => [
			'name' => 'Video CD',
			'type' => 'application/x-cdlink'
		],
		'vsf' => [
			'name' => 'Viewport+',
			'type' => 'application/vnd.vsf'
		],
		'wrl' => [
			'name' => 'Virtual Reality Modeling Language',
			'type' => 'model/vrml'
		],
		'vcx' => [
			'name' => 'VirtualCatalog',
			'type' => 'application/vnd.vcx'
		],
		'mts' => [
			'name' => 'Virtue MTS',
			'type' => 'model/vnd.mts'
		],
		'vtu' => [
			'name' => 'Virtue VTU',
			'type' => 'model/vnd.vtu'
		],
		'vis' => [
			'name' => 'Visionary',
			'type' => 'application/vnd.visionary'
		],
		'viv' => [
			'name' => 'Vivo',
			'type' => 'video/vnd.vivo'
		],
		'ccxml' => [
			'name' => 'Voice Browser Call Control',
			'type' => '"application/ccxml+xml,"'
		],
		'vxml' => [
			'name' => 'VoiceXML',
			'type' => 'application/voicexml+xml'
		],
		'src' => [
			'name' => 'WAIS Source',
			'type' => 'application/x-wais-source'
		],
		'wbxml' => [
			'name' => 'WAP Binary XML (WBXML)',
			'type' => 'application/vnd.wap.wbxml'
		],
		'wbmp' => [
			'name' => 'WAP Bitamp (WBMP)',
			'type' => 'image/vnd.wap.wbmp'
		],
		'wav' => [
			'name' => 'Waveform Audio File Format (WAV)',
			'type' => 'audio/x-wav'
		],
		'davmount' => [
			'name' => 'Web Distributed Authoring and Versioning',
			'type' => 'application/davmount+xml'
		],
		'woff' => [
			'name' => 'Web Open Font Format',
			'type' => 'application/x-font-woff'
		],
		'wspolicy' => [
			'name' => 'Web Services Policy',
			'type' => 'application/wspolicy+xml'
		],
		'webp' => [
			'name' => 'WebP Image',
			'type' => 'image/webp'
		],
		'wtb' => [
			'name' => 'WebTurbo',
			'type' => 'application/vnd.webturbo'
		],
		'wgt' => [
			'name' => 'Widget Packaging and XML Configuration',
			'type' => 'application/widget'
		],
		'hlp' => [
			'name' => 'WinHelp',
			'type' => 'application/winhlp'
		],
		'wml' => [
			'name' => 'Wireless Markup Language (WML)',
			'type' => 'text/vnd.wap.wml'
		],
		'wmls' => [
			'name' => 'Wireless Markup Language Script (WMLScript)',
			'type' => 'text/vnd.wap.wmlscript'
		],
		'wmlsc' => [
			'name' => 'WMLScript',
			'type' => 'application/vnd.wap.wmlscriptc'
		],
		'wpd' => [
			'name' => 'Wordperfect',
			'type' => 'application/vnd.wordperfect'
		],
		'stf' => [
			'name' => 'Worldtalk',
			'type' => 'application/vnd.wt.stf'
		],
		'wsdl' => [
			'name' => 'WSDL - Web Services Description Language',
			'type' => 'application/wsdl+xml'
		],
		'xbm' => [
			'name' => 'X BitMap',
			'type' => 'image/x-xbitmap'
		],
		'xpm' => [
			'name' => 'X PixMap',
			'type' => 'image/x-xpixmap'
		],
		'xwd' => [
			'name' => 'X Window Dump',
			'type' => 'image/x-xwindowdump'
		],
		'der' => [
			'name' => 'X.509 Certificate',
			'type' => 'application/x-x509-ca-cert'
		],
		'fig' => [
			'name' => 'Xfig',
			'type' => 'application/x-xfig'
		],
		'xhtml' => [
			'name' => 'XHTML - The Extensible HyperText Markup Language',
			'type' => 'application/xhtml+xml'
		],
		'xml' => [
			'name' => 'XML - Extensible Markup Language',
			'type' => 'application/xml'
		],
		'xdf' => [
			'name' => 'XML Configuration Access Protocol - XCAP Diff',
			'type' => 'application/xcap-diff+xml'
		],
		'xenc' => [
			'name' => 'XML Encryption Syntax and Processing',
			'type' => 'application/xenc+xml'
		],
		'xer' => [
			'name' => 'XML Patch Framework',
			'type' => 'application/patch-ops-error+xml'
		],
		'rl' => [
			'name' => 'XML Resource Lists',
			'type' => 'application/resource-lists+xml'
		],
		'rs' => [
			'name' => 'XML Resource Lists',
			'type' => 'application/rls-services+xml'
		],
		'rld' => [
			'name' => 'XML Resource Lists Diff',
			'type' => 'application/resource-lists-diff+xml'
		],
		'xslt' => [
			'name' => 'XML Transformations',
			'type' => 'application/xslt+xml'
		],
		'xop' => [
			'name' => 'XML-Binary Optimized Packaging',
			'type' => 'application/xop+xml'
		],
		'xpi' => [
			'name' => 'XPInstall - Mozilla',
			'type' => 'application/x-xpinstall'
		],
		'xspf' => [
			'name' => 'XSPF - XML Shareable Playlist Format',
			'type' => 'application/xspf+xml'
		],
		'xul' => [
			'name' => 'XUL - XML User Interface Language',
			'type' => 'application/vnd.mozilla.xul+xml'
		],
		'xyz' => [
			'name' => 'XYZ File Format',
			'type' => 'chemical/x-xyz'
		],
		'yang' => [
			'name' => 'YANG Data Modeling Language',
			'type' => 'application/yang'
		],
		'yin' => [
			'name' => 'YIN (YANG - XML)',
			'type' => 'application/yin+xml'
		],
		'zir' => [
			'name' => 'Z.U.L. Geometry',
			'type' => 'application/vnd.zul'
		],
		'zip' => [
			'name' => 'Zip Archive',
			'type' => 'application/zip'
		],
		'zmm' => [
			'name' => 'ZVUE Media Manager',
			'type' => 'application/vnd.handheld-entertainment+xml'
		],
		'zaz' => [
			'name' => 'Zzazz Deck',
			'type' => 'application/vnd.zzazz.deck+xml'
		],
	];

	public static function getType(string $extension, $default = null): ?string {
		if ($extension[0] === '.') $extension = substr($extension, 1);
		return self::$collection[$extension]['type']?? $default;
	}

	public static function getName(string $extension): ?string {
		if ($extension[0] === '.') $extension = substr($extension, 1);
		return self::$collection[$extension]['name'] ?? null;
	}

	public static function getExtension(string $type, $default = null): ?string {
		foreach (self::$collection as $extension => $mime)
			if ($mime['type'] === $type) return $extension;
		return $default;
	}

	public static function hasType(string $type): bool {
		foreach (self::$collection as $extension => $mime)
			if ($mime['type'] === $type) return true;
		return false;
	}

	public static function hasExtension(string $extension): bool {
		if ($extension[0] === '.') $extension = substr($extension, 1);
		return isset(self::$collection[$extension]);
	}
}