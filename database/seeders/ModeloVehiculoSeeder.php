<?php

namespace Database\Seeders;

use App\Models\MarcaVehiculo;
use App\Models\ModeloVehiculo;
use Illuminate\Database\Seeder;

class ModeloVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = MarcaVehiculo::pluck('id', 'nombre')->toArray();

        $modelos = [
            'Toyota' => ['Agya','Aygo','Yaris','Yaris Sedan','Yaris Hatchback','Vitz','Belta','Platz','Etios','Corolla','Corolla Cross','Corolla Fielder','Corolla Axio','Corolla Verso','Corolla Rumion','Auris','Allion','Premio','Avensis','Camry','Avalon','Crown','Century','Prius','Prius C','Prius Alpha','Aqua','Mirai','Starlet','Tercel','Corsa','Sprinter','Carina','Caldina','Corona','Mark II','Chaser','Cresta','Probox','Succeed','Ractis','Raum','Passo','Porte','Spade','Sienta','Wish','Isis','Innova','Avanza','Rush','Raize','C-HR','RAV4','Harrier','Venza','Highlander','Fortuner','4Runner','Land Cruiser Prado','Land Cruiser 70','Land Cruiser 300','FJ Cruiser','Sequoia','Hilux','Tacoma','Tundra','LiteAce','TownAce','Hiace','Granvia','Alphard','Vellfire','Noah','Voxy','Estima','Coaster','Dyna','ToyoAce'],
            'Nissan' => ['March','Micra','Tiida','Versa','Sentra','Sunny','Almera','Altima','Maxima','Bluebird','Bluebird Sylphy','Primera','Pulsar','Latio','Note','Wingroad','AD Wagon','Cube','Leaf','Skyline','350Z','370Z','GT-R','Kicks','Juke','Qashqai','X-Trail','Rogue','Murano','Pathfinder','Patrol','Armada','Terrano','Magnite','Frontier','Navara','NP300','D21','D22','Titan','Vanette','Serena','Elgrand','Urvan','Caravan','NV200','NV300','NV350','Cabstar','Atlas','Civilian'],
            'Suzuki' => ['Alto','Celerio','S-Presso','Swift','Baleno','Dzire','Ignis','Splash','Wagon R','Ciaz','Aerio','SX4','S-Cross','Fronx','Vitara','Grand Vitara','Vitara Brezza','Jimny','Samurai','Sidekick','XL7','Ertiga','APV','Carry','Super Carry','Every','Maruti 800','Kizashi'],
            'Chevrolet' => ['Spark','Spark GT','Beat','Joy','Celta','Corsa','Classic','Sail','Aveo','Sonic','Onix','Prisma','Cruze','Cavalier','Malibu','Impala','Optra','Astra','Vectra','Tracker','Trax','Groove','Captiva','Equinox','Blazer','Trailblazer','Tahoe','Suburban','Orlando','Spin','Zafira','Montana','S10','Colorado','Silverado','D-Max','Luv','N300','N400','Express','Cheyenne'],
            'Hyundai' => ['Atos','Eon','Grand i10','i10','i20','i30','Accent','Verna','Excel','Elantra','Avante','Sonata','Azera','Grandeur','Veloster','Ioniq','Ioniq 5','Ioniq 6','Venue','Creta','Kona','Tucson','Santa Fe','Palisade','Terracan','Galloper','Veracruz','Stargazer','Matrix','H-1','Starex','H100','Porter','County','Mighty','HD65','HD72','HD78','HD120','Xcient'],
            'Kia' => ['Picanto','Morning','Rio','Pride','Sephia','Spectra','Cerato','Forte','K3','K5','Optima','K7','Cadenza','Stinger','Soul','Sonet','Seltos','Stonic','Niro','Sportage','Sorento','Mohave','Telluride','Carens','Carnival','Sedona','Bongo','K2500','K2700','K3000','K3600','Pregio','Granbird'],
            'Honda' => ['Brio','Fit','Jazz','City','Civic','Accord','Insight','CR-Z','Integra','Prelude','Legend','Mobilio','Freed','Stream','Odyssey','BR-V','HR-V','CR-V','ZR-V','Pilot','Passport','Element','Ridgeline'],
            'Mitsubishi' => ['Mirage','Colt','Lancer','Lancer Evolution','Galant','Eclipse','Attrage','ASX','Eclipse Cross','Outlander','Montero','Montero Sport','Pajero','Pajero Mini','Nativa','Airtrek','Xpander','Delica','L200','Triton','Canter','Fuso Fighter','Fuso Rosa','Fuso Super Great'],
            'Mazda' => ['Mazda 2','Mazda 3','Mazda 5','Mazda 6','Mazda 323','Mazda 626','Demio','Familia','Axela','Atenza','Premacy','MX-5','RX-7','RX-8','CX-3','CX-30','CX-5','CX-50','CX-60','CX-7','CX-8','CX-9','CX-90','BT-50','B2200','B2500','B2600','MPV','Bongo'],
            'Ford' => ['Ka','Fiesta','Focus','Escort','Laser','Fusion','Mondeo','Taurus','Mustang','EcoSport','Territory','Escape','Edge','Explorer','Expedition','Bronco','Bronco Sport','Everest','Ranger','Maverick','F-100','F-150','F-250','F-350','F-450','Courier','Transit','Transit Connect','Econoline','Cargo'],
            'Volkswagen' => ['Gol','Voyage','Fox','Up','Polo','Virtus','Golf','Jetta','Vento','Passat','Bora','Santana','Beetle','New Beetle','Scirocco','Nivus','T-Cross','Taos','Tiguan','Touareg','Saveiro','Amarok','Caddy','Transporter','Caravelle','Multivan','Crafter','Kombi','Delivery','Constellation'],
            'Renault' => ['Kwid','Twingo','Clio','Symbol','Logan','Sandero','Stepway','Megane','Fluence','Laguna','Captur','Duster','Koleos','Arkana','Oroch','Kangoo','Master','Trafic','Dokker'],
            'Fiat' => ['Uno','Mobi','Palio','Siena','Grand Siena','Argo','Cronos','Punto','Tipo','Bravo','Linea','Panda','Strada','Toro','Pulse','Fastback','Idea','Doblo','Fiorino','Ducato','Fullback'],
            'Peugeot' => ['Partner','Expert','Boxer','Landtrek'],
            'Citroen' => ['C1','C2','C3','C3 Aircross','C4','C4 Cactus','C4 Lounge','C5','C5 Aircross','Xsara','Xsara Picasso','Berlingo','Jumpy','Jumper'],
            'Subaru' => ['Justy','Impreza','Legacy','WRX','WRX STI','Levorg','BRZ','XV','Crosstrek','Forester','Outback','Tribeca','Ascent'],
            'Isuzu' => ['D-Max','Rodeo','Trooper','MU','MU-X','Faster','KB','NHR','NPR','NKR','NQR','FRR','FTR','FVR','Giga','Journey','Erga'],
            'Jeep' => ['Renegade','Compass','Patriot','Cherokee','Grand Cherokee','Wrangler','Gladiator','Commander','Wagoneer','Grand Wagoneer'],
            'Dodge' => ['Neon','Dart','Avenger','Charger','Challenger','Journey','Durango','Caravan','Grand Caravan','Dakota','Ram 1500','Ram 2500','Ram 3500','Sprinter'],
            'RAM' => ['ProMaster','ProMaster City'],
            'Mercedes-Benz' => ['Clase A','Clase B','Clase C','Clase E','Clase S','CLA','CLS','GLA','GLB','GLC','GLE','GLS','Clase G','Vito','Viano','Clase V','Sprinter','Citan','Accelo','Atego','Axor','Actros','Arocs','O 500','OF 1721','LO 915'],
            'BMW' => ['Serie 1','Serie 2','Serie 3','Serie 4','Serie 5','Serie 6','Serie 7','Serie 8','X1','X2','X3','X4','X5','X6','X7','Z4','i3','i4','iX'],
            'Audi' => ['A1','A3','A4','A5','A6','A7','A8','Q2','Q3','Q5','Q7','Q8','TT','e-tron'],
            'Volvo' => ['S40','S60','S80','S90','V40','V60','V90','XC40','XC60','XC70','XC90','FM','FMX','FH','FL','B7R','B9R','B11R'],
            'Land Rover' => ['Defender','Discovery','Discovery Sport','Freelander','Range Rover','Range Rover Sport','Range Rover Evoque','Range Rover Velar'],
            'Changan' => ['Alsvin','Benni','Eado','Eado Plus','CS15','CS35','CS35 Plus','CS55','CS55 Plus','CS75','CS75 Plus','CS85','CS95','UNI-T','UNI-K','UNI-V','Hunter','Star','Honor','Karvaan'],
            'Great Wall' => ['C30','C50','M4','Wingle 5','Wingle 6','Wingle 7','Poer','Steed','Haval H1','Haval H2','Haval H3','Haval H5','Haval H6','Haval H9'],
            'Haval' => ['Jolion','H6','H6 GT','Dargo','H9','M6'],
            'JAC' => ['J2','J3','J4','J5','J6','JS2','JS3','JS4','JS6','JS8','S2','S3','S5','S7','T6','T8','T9','Sunray','Refine','X200','X400','N55','N65','N75','N90','N120'],
            'Geely' => ['CK','MK','GC6','Emgrand 7','Emgrand EC7','Emgrand GS','Coolray','Azkarra','Okavango','Monjaro','GX3 Pro','Geometry C'],
            'BYD' => ['F0','F3','F5','Qin','Dolphin','Seagull','Seal','Han','Song','Song Plus','Yuan','Yuan Plus','Tang','S1','S2','S6','T3','e2','e5','e6'],
            'Chery' => ['QQ','QQ3','Fulwin','Arrizo 3','Arrizo 5','Arrizo 6','Arrizo 8','Tiggo 2','Tiggo 3','Tiggo 4','Tiggo 5','Tiggo 7','Tiggo 7 Pro','Tiggo 8','Tiggo 8 Pro'],
            'DFSK' => ['Glory 330','Glory 500','Glory 560','Glory 580','Glory iX5','C31','C32','C35','C37','K01','K05','K07','K09','K17','EC35'],
            'Dongfeng' => ['Aeolus A30','Aeolus Yixuan','T5','T5 Evo','T7','Rich','Rich 6','Captain','Dolica','Duolika','Kingrun','KX','KR'],
            'Foton' => ['Gratour','View','View CS2','View G7','View G9','Tunland','Tunland G7','Tunland G9','Midi','Aumark','Auman','Ollin','Forland','BJ1039','BJ1049','BJ1069','BJ6120'],
            'BAIC' => ['D20','D50','X25','X35','X55','X65','X7','BJ20','BJ40','BJ60','BJ80','M20','M50','M60'],
            'GAC' => ['GA3','GA4','GA5','GA6','GS3','GS4','GS5','GS7','GS8','Emkoo','Empow','M6','M8','Aion Y','Aion S'],
            'MG' => ['MG3','MG5','MG6','GT','ZS','ZS EV','HS','RX5','One','Marvel R'],
            'Jetour' => ['X70','X70 Plus','X90','X90 Plus','Dashing','T1','T2','Traveller'],
            'Maxus' => ['T60','T70','T90','D60','D90','G10','G20','V80','V90','Deliver 9'],
            'Yutong' => ['E7S','E9','C9E','C13','C13 Pro','T15E','ZK6706','ZK6715','ZK6818','ZK6890','ZK6906','ZK6107','ZK6110','ZK6115','ZK6116','ZK6117','ZK6120','ZK6121','ZK6122','ZK6125','ZK6127','ZK6128','ZK6146'],
            'King Long' => ['XMQ6606','XMQ6700','XMQ6759','XMQ6800','XMQ6802','XMQ6900','XMQ6100','XMQ6110','XMQ6120','XMQ6127','XMQ6140'],
            'Higer' => ['KLQ6608','KLQ6702','KLQ6758','KLQ6800','KLQ6850','KLQ6898','KLQ6109','KLQ6119','KLQ6122','KLQ6125'],
            'Golden Dragon' => ['XML6606','XML6700','XML6807','XML6857','XML6907','XML6102','XML6112','XML6125'],
            'Zhongtong' => ['LCK6600','LCK6800','LCK6900','LCK6100','LCK6115','LCK6125'],
            'Marcopolo' => ['Senior','Torino','Torino Express','Viale','Viale BRT','Viaggio 900','Viaggio 1050','Paradiso 1200','Paradiso 1350','Paradiso 1600 LD','Paradiso 1800 DD','Ideale','Volare W8','Volare W9','Volare Fly','Volare Attack'],
            'Busscar' => ['Urbanuss','Urbanuss Pluss','El Buss','Vissta Buss','Vissta Buss DD','Jum Buss'],
            'Hino' => ['Dutro','XZU','GD','GH','FG','FM','FL','Ranger','Profia'],
            'Scania' => ['Serie P','Serie G','Serie R','Serie S','P 310','P 360','G 410','G 440','R 420','R 450','R 500','R 620','K 310','K 360','K 400','K 440'],
            'Volvo' => ['FL','FE','FM','FMX','FH','FH16','VM','NL10','NL12'],
            'Iveco' => ['Daily','Eurocargo','Tector','Stralis','Trakker','S-Way','Hi-Way','Bus Daily','Crossway'],
            'MAN' => ['TGL','TGM','TGS','TGX','Lion\'s City','Lion\'s Coach'],
            'Freightliner' => ['M2','FLD','Century','Columbia','Cascadia','Coronado','Business Class'],
            'International' => ['DuraStar','WorkStar','PayStar','TranStar','ProStar','LoneStar','LT','MV'],
            'Mack' => ['Granite','Anthem','Pinnacle','Vision','Titan','Super-Liner','CH','CXU'],
            'Kenworth' => ['T300','T370','T400','T600','T660','T680','T800','T880','W900','K100'],
            'Honda' => ['Navi','Wave','Biz','CB125F','CB190R','CB250','CB300','CB500','CBR250','CBR500','XR125','XR150L','XR190L','XR250 Tornado','XRE300','Africa Twin','GL150','Cargo 150'],
            'Yamaha' => ['Crypton','YBR125','YBR150','FZ16','FZ25','FZ-S','MT-03','MT-07','R3','XTZ125','XTZ150','XTZ250','Tenere 250','Tenere 700','WR250','NMax','XMax'],
            'Suzuki' => ['AX100','GN125','EN125','Gixxer 150','Gixxer 250','GSX-R150','DR150','DR200','DR650','V-Strom 250','V-Strom 650','Burgman'],
            'Kawasaki' => ['Boxer','Ninja 250','Ninja 300','Ninja 400','Z250','Z400','Z650','KLX150','KLX250','KLR650','Versys 300','Versys 650'],
            'Bajaj' => ['Boxer 100','Boxer 150','Platina','Discover','Pulsar 125','Pulsar 150','Pulsar 180','Pulsar 200 NS','Pulsar 200 RS','Dominar 250','Dominar 400','Avenger 220','Maxima','RE'],
            'TVS' => ['Sport','Radeon','Star City','Raider 125','Apache 160','Apache 180','Apache 200','Apache 310','Ntorq','King'],
            'Haojue' => ['DK125','DK150','NK150','DR160','KA150','TR150','Lindy','VS125'],
        ];

        foreach ($modelos as $marcaNombre => $lista) {
            $marcaId = $marcas[$marcaNombre] ?? null;
            if (! $marcaId) continue;
            foreach ($lista as $modelo) {
                ModeloVehiculo::create([
                    'marca_vehiculo_id' => $marcaId,
                    'nombre' => $modelo,
                    'estado' => true,
                ]);
            }
        }
    }
}