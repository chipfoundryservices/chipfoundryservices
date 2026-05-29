Semiconductor Manufacturing: The Epitaxial (Epi) Process

Epitaxy—from the Greek "epi" (upon) and "taxis" (arrangement)—is the controlled growth of a crystalline 
layer on a crystalline substrate where the deposited film inherits the crystal structure and orientation
 of the underlying material. It's one of the most critical processes in modern semiconductor manufacturi
ng, enabling precise control of material composition, doping profiles, and strain engineering at the ato
mic level.

Fundamental Concepts

Types of Epitaxy

Homoepitaxy involves growing the same material on itself (Si on Si). This is used when you need a purer 
or differently doped layer than the substrate can provide—for example, growing a lightly doped silicon l
ayer on a heavily doped substrate to create the active device region.

Heteroepitaxy involves growing a different material on the substrate (SiGe on Si, GaN on sapphire). This
 introduces the complexity of lattice mismatch, but enables strain engineering, bandgap engineering, and
 integration of materials with superior properties.

Why Epitaxy Matters

Unlike other deposition methods that produce polycrystalline or amorphous films, epitaxy produces single
-crystal material with:

Controlled and uniform doping profiles (box-shaped rather than Gaussian)
Atomically sharp interfaces between layers
The ability to introduce intentional strain for mobility enhancement
Lower defect densities than bulk crystal growth methods


Primary Epitaxial Techniques

1. Chemical Vapor Deposition (CVD) Epitaxy

This is the workhorse of silicon semiconductor manufacturing. Precursor gases decompose at a heated subs
trate surface, and the liberated atoms arrange themselves epitaxially.
Silicon precursors (in order of increasing deposition temperature):

• Silane (SiH4): Temp = 550–750°C | Rate = Low | Use = High purity, thin layers
• Dichlorosilane (DCS): Temp = 750–950°C | Rate = Medium | Use = Selective Epitaxy (Standard)
• Trichlorosilane (TCS): Temp = 950–1100°C | Rate = High | Use = Bulk growth
• Silicon Tetrachloride (SiCl4): Temp = 1100–1250°C | Rate = Very High | Use = Polishing / cleaning

The choice involves tradeoffs. Lower temperatures preserve previously fabricated structures and reduce d
opant diffusion, but higher temperatures generally yield better crystal quality and faster growth. Chlor
inated precursors also enable selective growth because they etch silicon while depositing—the deposition
 wins on crystalline surfaces while etching wins on dielectric surfaces.
Dopant gases:

N-type: phosphine (PH₃), arsine (AsH₃)
P-type: diborane (B₂H₆)

In-situ doping during epitaxy produces far sharper doping transitions than ion implantation followed by 
annealing.

2. Molecular Beam Epitaxy (MBE)

MBE operates in ultra-high vacuum (10⁻¹⁰ to 10⁻¹¹ Torr). Source materials are heated in effusion cells, 
and molecular beams travel ballistically to the substrate. Growth rates are slow (roughly 1 monolayer pe
r second), but control is exquisite.

The UHV environment allows real-time monitoring via reflection high-energy electron diffraction (RHEED),
 letting operators observe growth layer by layer. MBE excels at growing quantum wells, superlattices, an
d structures requiring atomic-level precision. It's less common in high-volume silicon manufacturing due
 to low throughput but remains important for III-V compound semiconductors and research.

3. Metal-Organic CVD (MOCVD)

MOCVD uses metal-organic precursors like trimethylgallium (TMGa), trimethylaluminum (TMAl), and trimethy
lindium (TMIn), combined with hydrides like ammonia or arsine. It's the dominant technique for III-V and
 III-N compound semiconductors.

MOCVD is how the world makes:

LEDs (GaN-based)
Laser diodes
High-electron-mobility transistors (HEMTs)
Multi-junction solar cells

The technique offers higher throughput than MBE while maintaining good interface control for these compo
und materials.

Equipment and Reactor Design

Reactor Configurations

• Horizontal Reactor: Flow = Parallel to wafer | Limitation = Depletion effects | Use = Simple/Older
• Vertical (Pancake): Flow = Downward to susceptor | Advantage = Better uniformity | Use = Batch
• Barrel Reactor: Flow = Vertical over barrel | Advantage = High Throughput | Use = Older Batch
• Single-Wafer (Rotating): Flow = Downward + Rotation | Advantage = Best Control/Uniformity

The Single-Wafer (Rotating) configuration is used for modern advanced nodes.

Single-Wafer vs. Batch Processing

Modern logic fabs almost exclusively use single-wafer tools because:

Better within-wafer uniformity (<1% thickness variation)
Faster feedback for process control
Easier integration of in-situ cleaning and multi-step processes
More suitable for selective epitaxy with pattern-dependent effects

Batch reactors still find use in power devices, MEMS, and applications where throughput matters more tha
n atomic-level control.

Process Parameters and Their Effects

• Temperature: High/Low → Controls crystal quality (mobility) vs diffusion (abruptness)
• Pressure: 10-100 Torr (RPCVD) → Controls boundary layer & selective growth capability
• Gas Flow/Ratio: Si:Ge / Si:Dopant → Determines composition (strain) and doping profile
• Growth Rate: 0.01 - 10 um/min → Trade-off between throughput and atomic control

Selective Epitaxial Growth (SEG)

SEG grows epitaxial material only in exposed silicon regions, not on surrounding dielectrics. This is ac
hieved through careful chemistry—chlorine-containing precursors simultaneously deposit and etch, with ne
t deposition occurring only on crystalline surfaces where nucleation is favorable.

Challenges in Selective Growth

Loading effects: Growth rate depends on the ratio of exposed silicon to total area. A wafer with 10% sil
icon exposure will have different growth rates than one with 50% exposure, even with identical process c
onditions.

Faceting: Different crystallographic planes have different growth rates. In a trench, you might see (100
) surfaces growing at a different rate than (111) facets, creating non-rectangular shapes.

Pattern dependency: Narrow trenches behave differently than wide ones due to differences in reactant tra
nsport.

Applications of SEG

Raised source/drain: Growing elevated silicon or SiGe in the source/drain regions reduces parasitic resi
stance and enables better silicide formation.
Embedded SiGe (eSiGe): Growing SiGe in etched source/drain cavities adjacent to the channel creates comp
ressive strain that enhances hole mobility in pMOS devices.
Channel material for GAA: Growing superlattice stacks of Si and SiGe that are later processed into gate-
all-around nanosheet channels.


Strain Engineering Through Epitaxy

This is where epitaxy becomes strategically essential for modern logic devices.
Compressive Strain for pMOS
Intel introduced embedded SiGe source/drain at the 90nm node. The process:

Etch cavities in the source/drain regions adjacent to the gate
Epitaxially grow Si₁₋ₓGeₓ (typically x = 0.2–0.4) in these cavities
The larger lattice constant of SiGe pushes against the channel, creating compressive strain

Compressive strain increases hole mobility by 50% or more, dramatically improving pMOS drive current.

Tensile Strain for nMOS

Si:C (carbon-doped silicon) has a smaller lattice constant than pure silicon. Growing Si:C in nMOS sourc
e/drain regions creates tensile strain in the channel, improving electron mobility. This is more challen
ging than SiGe because carbon tends to precipitate rather than substitutionally incorporate, and the ach
ievable strain is lower.

Strain in FinFETs and Beyond

In FinFET architectures, epitaxial source/drain regions are grown on the exposed fin sidewalls and tops 
after gate formation. The epitaxial material merges between adjacent fins to form enlarged, low-resistan
ce source/drain contacts. The shape and composition of this epitaxy significantly impacts both strain an
d parasitic resistance.

Epitaxy for Gate-All-Around (GAA) Transistors
GAA nanosheet transistors—now in production at leading logic fabs—rely critically on epitaxial superlatt
ice growth.

The Process Concept

Grow a superlattice of alternating Si and SiGe layers (typically 3–5 periods, each layer perhaps 5–10nm 
thick)
Pattern fins through this superlattice stack
During gate formation, selectively etch away the SiGe layers, leaving suspended silicon nanosheets
Wrap the gate around all surfaces of these nanosheets

Epitaxy Challenges for GAA

Thickness control: Each nanosheet must be extremely uniform in thickness because device characteristics 
depend on it. Variations of even 1–2 Ångstroms matter.

Interface abruptness: The Si/SiGe interfaces must be atomically sharp. Any intermixing affects the sele
ctivity of the later SiGe removal etch.

Defect density: Any stacking faults or dislocations threading through the superlattice will degrade or k
ill devices.

Composition uniformity: The SiGe layers need consistent Ge content throughout for uniform etch behavior.

Quality Metrics and Characterization

• Thickness: Tool = Ellipsometry / TEM | Precision = Angstrom-level
• Composition: Tool = XRD / SIMS | Metric = Ge fraction / Strain state
• Doping: Tool = SIMS / 4-Point Probe | Metric = Depth Profile / Sheet Res
• Crystallinity: Tool = XRD Rocking Curve / TEM | Metric = Defect Density
• Roughness: Tool = AFM (Atomic Force Micro) | Metric = RMS < 1A

Defects in Epitaxial Films

Stacking faults occur when the atomic stacking sequence is disrupted. They can originate from substrate 
defects, particles, or growth instabilities.

Threading dislocations are line defects that propagate from the substrate-film interface through the fil
m. In heteroepitaxy with lattice mismatch, dislocations form to relieve strain once the film exceeds a c
ritical thickness.

Misfit dislocations lie at the interface between mismatched materials. Controlled introduction of these 
can be used intentionally in graded buffer layers.

Particles and contamination nucleate defects. Pre-epitaxial cleaning (typically an HF-based native oxide
 removal followed by an in-situ high-temperature hydrogen bake) is critical.

Advanced and Emerging Directions

Lower Temperature Processing

As devices shrink and thermal budgets tighten, there's strong pressure to reduce epitaxial growth temper
atures. This has driven interest in:

Plasma-enhanced CVD (PECVD) epitaxy
Novel precursors that decompose at lower temperatures
Atomic layer epitaxy (ALE) approaches

III-V on Silicon Integration

Growing III-V compound semiconductors (GaAs, InGaAs, InP) on silicon substrates would enable integration
 of superior III-V device performance with silicon CMOS economics. The challenge is the large lattice mi
smatch (about 4% for GaAs on Si) and different thermal expansion coefficients. Approaches include:

Graded buffer layers

Aspect ratio trapping (growing III-V in narrow trenches where threading dislocations terminate on sidewa
lls)
Wafer bonding of pre-grown III-V layers

2D Material Integration

Looking further ahead, there's research interest in epitaxial or quasi-epitaxial growth of 2D materials 
(graphene, transition metal dichalcogenides) on conventional substrates.

Summary

Epitaxy has evolved from a method of growing purer silicon layers to an essential enabling technology fo
r strain engineering, sharp junction formation, and advanced transistor architectures. The transition to
 FinFETs increased its importance; the move to gate-all-around nanosheet devices has made it even more c
entral to leading-edge manufacturing.

The fundamental principles—crystalline growth via surface chemistry, careful temperature and pressure con
trol, selective deposition through chemistry manipulation—remain consistent across applications. What c
ontinues to advance is the precision: tighter thickness tolerances, sharper interfaces, lower defect den
sities, and more complex multi-layer structures, all driven by the relentless demands of transistor scal
ing.


Further Research:


Epitaxy comes from the Greek words "epi" (upon) and "taxis" (arrangement). It refers to the deposition o
f a crystalline overlayer on a crystalline substrate, where the overlayer is in registry (aligned) with 
the substrate's crystal structure.

Types of Epitaxy

• 1. Homoepitaxy: Growing a film of the same material as the substrate (e.g., Si on Si)
• 2. Heteroepitaxy: Growing a film of a different material than the substrate (e.g., SiGe on Si, GaAs on
 Si)

Main Epitaxial Deposition Methods

1. Vapor Phase Epitaxy (VPE) / Chemical Vapor Deposition (CVD)
This is the most common method in silicon semiconductor manufacturing.
Process:

• Precursor gases are introduced into a heated reactor chamber
• Chemical reactions occur at or near the substrate surface
• Atoms/molecules deposit epitaxially on the substrate

Common precursors for silicon epitaxy:

• Silane (SiH4)
• Dichlorosilane (SiH2Cl2 or DCS)
• Trichlorosilane (SiHCl3 or TCS)
• Silicon tetrachloride (SiCl4)

Temperature ranges:

• Low temperature: 550-750°C (using SiH4)
• Medium temperature: 750-950°C (using DCS)
• High temperature: 950-1150°C (using TCS or SiCl4)

Doping:

• N-type: Phosphine (PH3), Arsine (AsH3)
• P-type: Diborane (B2H6)

2. Molecular Beam Epitaxy (MBE)

Process:

• Ultra-high vacuum environment (10^-10 to 10^-11 Torr)
• Source materials are heated in effusion cells
• Molecular beams are directed at the substrate
• Very slow growth rate but excellent control

Advantages:

• Atomic-level precision
• Sharp interfaces
• In-situ monitoring (RHEED)
• Lower growth temperatures

Applications:

• III-V compound semiconductors
• Quantum wells, superlattices
• Research and specialized devices

3. Metal-Organic Chemical Vapor Deposition (MOCVD) / MOVPE

Process:

• Uses metal-organic precursors
• Important for compound semiconductors (GaN, GaAs, InP)

Common precursors:

• Trimethylgallium (TMGa)
• Trimethylaluminum (TMAl)
• Trimethylindium (TMIn)
• Ammonia (NH3) for nitrides

Applications:

• LEDs
• Laser diodes
• High-electron mobility transistors (HEMTs)
• Solar cells

Key Parameters in Epitaxial Growth

• Temperature: Affects growth rate, crystallinity, dopant incorporation
• Pressure: Atmospheric, reduced pressure, or low pressure
• Gas flow rates: Determines growth rate and uniformity
• Growth rate: Typically 0.1-10 μm/min for CVD
• V/III ratio (for compound semiconductors)

Epitaxy Equipment

Single Wafer vs. Batch Reactors
Single wafer reactors:

• Better uniformity control
• Faster cycle times
• Preferred for advanced nodes
• Examples: Applied Materials Centura, ASM Epsilon

Batch reactors:

• Higher throughput
• Lower cost per wafer
• Used for less critical applications

Reactor Configurations

• Horizontal reactors: Gas flows horizontally over the wafer
• Vertical (pancake) reactors: Gas flows vertically
• Barrel reactors: Wafers on a rotating susceptor
• Single wafer rotating disk: Most common for advanced applications

Critical Quality Parameters

• Thickness uniformity: Typically <1% within wafer, <2% wafer-to-wafer
• Resistivity (doping) uniformity: Related to dopant incorporation
• Defect density: Stacking faults, dislocations, particles
• Surface roughness: Measured by AFM
• Crystal quality: Measured by X-ray diffraction

Selective Epitaxial Growth (SEG)

Concept: Growing epitaxial layers only in specific regions (e.g., in trenches or openings in oxide)

Key challenges:

• Loading effect: Growth rate depends on exposed Si area
• Faceting: Different crystallographic planes grow at different rates
• Pattern dependency

Applications:

• Raised source/drain
• Embedded SiGe for strain engineering
• 3D NAND

Epitaxy in Modern Semiconductor Manufacturing

1. Strain Engineering

SiGe source/drain (for pMOS):

• Creates compressive strain in channel
• Improves hole mobility
• Used since 90nm node (Intel)

Si:C source/drain (for nMOS):

• Creates tensile strain in channel
• Improves electron mobility

2. FinFET and GAA Applications

• Superlattice growth (Si/SiGe) for nanosheet GAA
• High aspect ratio epitaxy
• Selective epitaxy for source/drain regions

3. Advanced Doping

• In-situ doping during epitaxy
• Box-shaped doping profiles
• Ultra-shallow junctions

Challenges and Future Trends

• Precision control: Atomic-level thickness control for GAA
• Defect reduction: Critical for yield
• Lower temperature processes: To preserve underlying structures
• New materials: SiGe with varying Ge content, III-V integration
• Selective growth: More complex geometries

Epitaxy vs. Other Deposition Methods

• Crystallinity: Epitaxy = Single Crystal | Standard CVD = Poly-Amorphous | PVD = Poly-Amorphous
• Temperature: Epitaxy = High | Standard CVD = Medium-High | PVD = Low-Medium
• Growth Rate: Epitaxy = Moderate | Standard CVD = Fast | PVD = Moderate
• Step Coverage: Epitaxy = Conformal | Standard CVD = Varies | PVD = Poor
• Interface Quality: Epitaxy = Excellent | Standard CVD = Good | PVD = Fair
