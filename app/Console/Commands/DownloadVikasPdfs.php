<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class DownloadVikasPdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vikas:download-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download PDFs from vikascollege.org and save to public/vikas/wp-content';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tumhari list yahan rakho
        $urls = [
'https://vikascollege.org/vikas/criterion_i_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/1.1.1_effective-curriculum-planning-and-delivery-through-a-well-planned-and-documented-process.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/1.2.1-1.2.2_Add-on-Courses-Reports.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/1.3.1_Crosscutting-Issues.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/1.3.2_students-undertaking-project-workfield-work-internships.pdf',
'https://vikascollege.org/vikas/criterion_ii_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.1.1.2_Final-admission-list-as-published-by-the-HEI.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.1.2.2._Caste-wise-first-year-enrollment-list.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.3.1_Students-Centric-Methods.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.4.1_Teachers-List.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.4.2.1_Copies-of-Ph.D.D.Sc-D.Litt_.-L.L.D-awareded.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.5.1_Tranparancy-in-Mechanism-of-Internal-external-assessment.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.6.1_Programme-Outcomes.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.6.2_Course-Outcomes.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2.6.3_Student-Passing-percentage.pdf',
'https://vikascollege.org/vikas/criterion_iii_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.2.1_Innovative-Ecosystem.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.2.2_Seminars-on-Research-Methodology.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.3.1_Link-to-Papers.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.3.1_Full-papers-published.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.3.2_Chapters-in-Books.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.4.1_Extension-Activities.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.4.2_Awards.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.4.3_Ext.-Activities.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.5.1_Links.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.5.1A_MOUs-Activities.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.5.1B_Professional-Training-Certificates.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.5.1C_Professional-Training-Certificates.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/3.5.1D_Professional-Training-Certificates.pdf',
'https://vikascollege.org/vikas/criterion_iv_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/4.1.1_Infrastructure.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/4.1.2-_Audited-Statements.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2018-2019.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2019-2020.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2020-2021.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2021-2022.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/2022-2023-1.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/4.2.1_Library-as-Learning-Resource.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/4.3.1_IT-Infrastructure.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/4.3.2.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/4.3.2_computer-configuration.pdf',
'https://vikascollege.org/vikas/criterion_v_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.1_links.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.1_a_POLICY-DOCUMENTS-FOR-SCHOLARSHIPS.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.1_b_Students-benefitted-by-scholarship-and-freeship.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.2_capacity-development-and-skills-enhancement-activities.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.3_students-benefitted-by-guidance-for-competitive-examinations-and-career-counseling.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.4_LINKS.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.4_a_-Zero-Tolerance-Policy-Document.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.4_b_ICC-MECHANISAM.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.4_c_Committees-for-Grievance-Redressal.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.1.4_d_ICC_WDC_COUSELING-REPORTS.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.2.1_Placement-letters.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.2.2_students-qualifying-in-statenational-international-level-examinations.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.3.1_awardsmedals-for-outstanding-performance-in-sportscultural-activities.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/5.4.1_Involvement-of-Alumni.pdf',
'https://vikascollege.org/vikas/criterion_vi_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.1.1_PERSPECTIVE-PLAN.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.2.1_DEPLOYMENT-OF-PERSPECTIVE-PLAN.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.2.2-Links.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.2.2._a_SCREEN-SHOT-ERP.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.2.2._b_ERP-AUDITED-STATEMENT.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.2.2_c_Report-on-E-governance.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.3.1_welfare-measures.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.3.2_financial-assistance-to-teachers.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.3.3_Certificates-of-FDP.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.5.1_Report-on-IQAC-Activities.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/6.5.2_MOU_LIST-OF-ACTIVITIES.pdf',
'https://vikascollege.org/vikas/criterion_vii_ssr-documents/',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.1_Report-on-Gender-Equity.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.4_Inclusive-Practices.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2-7.1.3-Links.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2_A_Policy-Document-on-Green-Campus-Initiatives.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2_B_-Photographs.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2_7.1.3_c_Reports.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.3_ENERGY-AUDIT.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/E-recycling-certificate.pdf',
'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.2.1_Best-Practices.pdf',

        ];

        $total = count($urls);
        $this->info("Found {$total} URLs to process.");

        // Progress bar start
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($urls as $url) {

            try {
                // "vikas/wp-content/" ke baad ka part nikalna
                if (strpos($url, 'vikas/wp-content/') !== false) {
                    $parts = explode('vikas/wp-content/', $url);
                    $relativePath = end($parts); // e.g. "uploads/2024/09/filename.pdf"
                } else {
                    // Agar direct wp-content structure nahi hai, to migrated folder me daal do
                    $relativePath = 'uploads/migrated/' . basename($url);
                }

                // Final local path
                $localPath = public_path("vikas/wp-content/" . $relativePath);

                // Ensure directory exists
                $directory = dirname($localPath);
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                // ✅ 1) Pehle check karo file already hai kya
                if (File::exists($localPath)) {
                    $this->newLine();
                    $this->info("Already exists, skipping: " . $relativePath);
                    $bar->advance();
                    continue;
                }

                // ✅ 2) Download process
                $response = Http::timeout(120)
                    ->withoutVerifying()
                    ->get($url);

                // Agar request hi successful nahi hui
                if (!$response->successful()) {
                    $this->newLine();
                    $this->error("Failed to download (HTTP error): " . $url);
                    $bar->advance();
                    continue;
                }

                // ✅ 3) Check if content is actually a PDF
                $contentType = $response->header('Content-Type');

                // Kuch servers "application/pdf; charset=binary" bhi bhejte hain
                if (!$contentType || strpos($contentType, 'application/pdf') === false) {
                    $this->newLine();
                    $this->error("Not a PDF, skipping: " . $url . " [Content-Type: " . ($contentType ?? 'N/A') . "]");
                    $bar->advance();
                    continue;
                }

                // ✅ 4) Ab actual PDF save karo
                File::put($localPath, $response->body());

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("All done! Files are now in public/vikas/wp-content/");
    }
}
