<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\WhmisHazardClass;

class ChemicalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Get hazard class IDs for common types
        $acuteToxicityId = WhmisHazardClass::where('class_name', 'Acute Toxicity')->first()->id;
        $healthHazardId = WhmisHazardClass::where('class_name', 'Health Hazard')->first()->id;
        $corrosiveId = WhmisHazardClass::where('class_name', 'Corrosive Materials')->first()->id;

        $chemicals = [
            ['cas' => '60-31-1', 'name' => 'Acetylcholine Chloride', 'whmis_hazard_class_id' => $acuteToxicityId],
            ['cas' => '9031-72-5', 'name' => 'Alcohol Dehydrogenase', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '53-84-9', 'name' => 'b-Nicotinamide Adenine Dinucleotide', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => 'Z00162925', 'name' => 'Garlon 4 Herbicide', 'whmis_hazard_class_id' => $acuteToxicityId],
            ['cas' => 'Z00162928', 'name' => 'Lontrel Herbicide', 'whmis_hazard_class_id' => $acuteToxicityId],
            ['cas' => 'Z00162929', 'name' => 'Lorsban 4E Insecticide', 'whmis_hazard_class_id' => $acuteToxicityId],
            ['cas' => '333338-18-4', 'name' => '4-Nitrophenyl phosphate disodium salt hexahydrate BioChemika, for enzyme immunoassay, >=99.0% (enzymatic)', 'whmis_hazard_class_id' => $corrosiveId],
            ['cas' => '56401-20-8', 'name' => 'a-D-Glucose 1-Phosphate', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '830-81-9', 'name' => 'a-Naphthyl Acetate', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '51963-61-2', 'name' => 'Adenosine 5\'-triphosphate disodium salt', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '687-69-4', 'name' => 'Ala-Gly', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => 'Z00162921', 'name' => 'Anti-Pig Whole Serum', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '55-27-6', 'name' => 'Arterenol Hydrochloride', 'whmis_hazard_class_id' => $acuteToxicityId],
            ['cas' => '1523-11-1', 'name' => 'b-Naphthyl Acetate', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '1184-16-3', 'name' => 'b-Nicotinamide Adenine Dinucleotide Phosphate Sodium Salt', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '9007-43-6', 'name' => 'Cytochrome c from bovine heart practical grade, >=60% as based on Mol. Wt. 12,327', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '91080-16-9', 'name' => 'Deoxyribonucleic acid-cellulose double-stranded from calf thymus DNA lyophilized powder', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '1655-52-3', 'name' => 'DNP-Alanine', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '1084-76-0', 'name' => 'DNP-Glycine', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '31356-36-2', 'name' => 'DNP-Norleucine', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '1655-54-5', 'name' => 'DNP-Phenylalanine', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '1694-97-9', 'name' => 'DNP-Valine', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '15518-68-0', 'name' => 'Fast Blue BBSalt hemi(zinc chloride) salt Standard Fluka, for microscopy (Hist.)', 'whmis_hazard_class_id' => $corrosiveId],
            ['cas' => '1999-34-4', 'name' => 'Gly-DL-Met', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '556-50-3', 'name' => 'Gly-Gly', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '869-19-2', 'name' => 'Gly-Leu', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '3321-03-7', 'name' => 'Gly-Phe', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '17123-30-7', 'name' => 'Gly-Phe-Ala', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '1963-21-9', 'name' => 'Gly-Val', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '87-51-4', 'name' => 'Indole-3-Acetic Acid', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '525-79-1', 'name' => 'Kinetin', 'whmis_hazard_class_id' => $healthHazardId],
            ['cas' => '8002-43-5', 'name' => 'L-a-Phosphatidylcholine', 'whmis_hazard_class_id' => $healthHazardId],
        ];

        DB::table('chemicals')->insert($chemicals);
    }
}
