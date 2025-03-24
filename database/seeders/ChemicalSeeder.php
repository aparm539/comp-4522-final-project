<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Shelf;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\Location;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChemicalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $chemicals = [
            ['cas' => '60-31-1', 'name' => 'Acetylcholine Chloride'],
            ['cas' => '9031-72-5', 'name' => 'Alcohol Dehydrogenase'],
            ['cas' => '53-84-9', 'name' => 'b-Nicotinamide Adenine Dinucleotide'],
            ['cas' => 'Z00162925', 'name' => 'Garlon 4 Herbicide'],
            ['cas' => 'Z00162928', 'name' => 'Lontrel Herbicide'],
            ['cas' => 'Z00162929', 'name' => 'Lorsban 4E Insecticide'],
            ['cas' => '333338-18-4', 'name' => '4-Nitrophenyl phosphate disodium salt hexahydrate BioChemika, for enzyme immunoassay, >=99.0% (enzymatic)'],
            ['cas' => '56401-20-8', 'name' => 'a-D-Glucose 1-Phosphate'],
            ['cas' => '830-81-9', 'name' => 'a-Naphthyl Acetate'],
            ['cas' => '51963-61-2', 'name' => 'Adenosine 5\'-triphosphate disodium salt'],
            ['cas' => '687-69-4', 'name' => 'Ala-Gly'],
            ['cas' => 'Z00162921', 'name' => 'Anti-Pig Whole Serum'],
            ['cas' => '55-27-6', 'name' => 'Arterenol Hydrochloride'],
            ['cas' => '1523-11-1', 'name' => 'b-Naphthyl Acetate'],
            ['cas' => '1184-16-3', 'name' => 'b-Nicotinamide Adenine Dinucleotide Phosphate Sodium Salt'],
            ['cas' => '9007-43-6', 'name' => 'Cytochrome c from bovine heart practical grade, >=60% as based on Mol. Wt. 12,327'],
            ['cas' => '91080-16-9', 'name' => 'Deoxyribonucleic acid-cellulose double-stranded from calf thymus DNA lyophilized powder'],
            ['cas' => '1655-52-3', 'name' => 'DNP-Alanine'],
            ['cas' => '1084-76-0', 'name' => 'DNP-Glycine'],
            ['cas' => '31356-36-2', 'name' => 'DNP-Norleucine'],
            ['cas' => '1655-54-5', 'name' => 'DNP-Phenylalanine'],
            ['cas' => '1694-97-9', 'name' => 'DNP-Valine'],
            ['cas' => '15518-68-0', 'name' => 'Fast Blue BBSalt hemi(zinc chloride) salt Standard Fluka, for microscopy (Hist.)'],
            ['cas' => '1999-34-4', 'name' => 'Gly-DL-Met'],
            ['cas' => '556-50-3', 'name' => 'Gly-Gly'],
            ['cas' => '869-19-2', 'name' => 'Gly-Leu'],
            ['cas' => '3321-03-7', 'name' => 'Gly-Phe'],
            ['cas' => '17123-30-7', 'name' => 'Gly-Phe-Ala'],
            ['cas' => '1963-21-9', 'name' => 'Gly-Val'],
            ['cas' => '87-51-4', 'name' => 'Indole-3-Acetic Acid'],
            ['cas' => '525-79-1', 'name' => 'Kinetin'],
            ['cas' => '8002-43-5', 'name' => 'L-a-Phosphatidylcholine']
        ];

        DB::table('chemicals')->insert($chemicals);
    }
    }
