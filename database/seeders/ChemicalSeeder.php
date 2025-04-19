<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['cas' => '60-31-1', 'name' => 'Acetylcholine Chloride', 'ishazardous' => true],
            ['cas' => '9031-72-5', 'name' => 'Alcohol Dehydrogenase', 'ishazardous' => true],
            ['cas' => '53-84-9', 'name' => 'b-Nicotinamide Adenine Dinucleotide', 'ishazardous' => true],
            ['cas' => 'Z00162925', 'name' => 'Garlon 4 Herbicide', 'ishazardous' => true],
            ['cas' => 'Z00162928', 'name' => 'Lontrel Herbicide', 'ishazardous' => true],
            ['cas' => 'Z00162929', 'name' => 'Lorsban 4E Insecticide', 'ishazardous' => true],
            ['cas' => '333338-18-4', 'name' => '4-Nitrophenyl phosphate disodium salt hexahydrate BioChemika, for enzyme immunoassay, >=99.0% (enzymatic)', 'ishazardous' => true],
            ['cas' => '56401-20-8', 'name' => 'a-D-Glucose 1-Phosphate', 'ishazardous' => true],
            ['cas' => '830-81-9', 'name' => 'a-Naphthyl Acetate', 'ishazardous' => true],
            ['cas' => '51963-61-2', 'name' => 'Adenosine 5\'-triphosphate disodium salt', 'ishazardous' => true],
            ['cas' => '687-69-4', 'name' => 'Ala-Gly', 'ishazardous' => true],
            ['cas' => 'Z00162921', 'name' => 'Anti-Pig Whole Serum', 'ishazardous' => true],
            ['cas' => '55-27-6', 'name' => 'Arterenol Hydrochloride', 'ishazardous' => true],
            ['cas' => '1523-11-1', 'name' => 'b-Naphthyl Acetate', 'ishazardous' => true],
            ['cas' => '1184-16-3', 'name' => 'b-Nicotinamide Adenine Dinucleotide Phosphate Sodium Salt', 'ishazardous' => true],
            ['cas' => '9007-43-6', 'name' => 'Cytochrome c from bovine heart practical grade, >=60% as based on Mol. Wt. 12,327', 'ishazardous' => true],
            ['cas' => '91080-16-9', 'name' => 'Deoxyribonucleic acid-cellulose double-stranded from calf thymus DNA lyophilized powder', 'ishazardous' => true],
            ['cas' => '1655-52-3', 'name' => 'DNP-Alanine', 'ishazardous' => true],
            ['cas' => '1084-76-0', 'name' => 'DNP-Glycine', 'ishazardous' => true],
            ['cas' => '31356-36-2', 'name' => 'DNP-Norleucine', 'ishazardous' => true],
            ['cas' => '1655-54-5', 'name' => 'DNP-Phenylalanine', 'ishazardous' => true],
            ['cas' => '1694-97-9', 'name' => 'DNP-Valine', 'ishazardous' => true],
            ['cas' => '15518-68-0', 'name' => 'Fast Blue BBSalt hemi(zinc chloride) salt Standard Fluka, for microscopy (Hist.)', 'ishazardous' => true],
            ['cas' => '1999-34-4', 'name' => 'Gly-DL-Met', 'ishazardous' => true],
            ['cas' => '556-50-3', 'name' => 'Gly-Gly', 'ishazardous' => true],
            ['cas' => '869-19-2', 'name' => 'Gly-Leu', 'ishazardous' => true],
            ['cas' => '3321-03-7', 'name' => 'Gly-Phe', 'ishazardous' => true],
            ['cas' => '17123-30-7', 'name' => 'Gly-Phe-Ala', 'ishazardous' => true],
            ['cas' => '1963-21-9', 'name' => 'Gly-Val', 'ishazardous' => true],
            ['cas' => '87-51-4', 'name' => 'Indole-3-Acetic Acid', 'ishazardous' => true],
            ['cas' => '525-79-1', 'name' => 'Kinetin', 'ishazardous' => true],
            ['cas' => '8002-43-5', 'name' => 'L-a-Phosphatidylcholine', 'ishazardous' => true],
        ];

        DB::table('chemicals')->insert($chemicals);
    }
}
